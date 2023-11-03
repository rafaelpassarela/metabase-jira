<?php

namespace App\Http\Controllers;

use App\Enums\PersonaTypeEnum;
use App\Helpers\Rocket;
use App\Models\Config;
use App\Models\Filters;
use App\Models\Issues;
use App\Models\IssuesPersonas;
use Illuminate\Support\Facades\Http;

use function PHPUnit\Framework\isEmpty;

class IssuesController extends Controller
{
    private $personasController;
    private $projectsController;
    private $console;
    private $date;
    private $baseURL;
    private $parentIssues;
    private $cachedIssues;
    private $logMessage;
    private $rocket;

    public function __construct(PersonasController $personasController, ProjectsController $projectsController, Rocket $rocket) {
        $this->baseURL = env('JIRA_URL');
        $this->console = false;
        $this->date = -1;
        $this->logMessage = '';

        $this->personasController = $personasController;
        $this->projectsController = $projectsController;

        $this->rocket = $rocket;

        $this->initCache();
    }

    public function setConsole(bool $value) {
        $this->console = $value;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    function initCache() {
        $this->parentIssues = array();
        $this->cachedIssues = array();
    }

    function message(string $value) {
        if ($this->console) {
            echo "$value \n";
        }

        $this->logMessage = $this->logMessage . "$value\n";
    }

    function requestIssue(string $filter) {
        $url = $this->baseURL . '/rest/api/3/search';

        $body = array(
            "expand" => array(""),
            "fields" => array(
                "*navigable"
            ),
            "fieldsByKeys" => false,
            "jql" => $filter,
            "maxResults" => 150,
            "startAt" => 0
        );

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . env('JIRA_TOKEN')
        ])->post($url, $body);

        if ($response->ok()) {
            return $response->json();
        } else {
            $this->message($response->status() . ' ' . $response->body());
            return null;
        }
    }

    function encodeFilter(string $filter, string $doneFilter) {
        // updated >= '2023-09-13 00:00' AND updated <= '2023-09-13 23:59' and resolution != null and issueType != Epic order by Key
        $date = "updated >= '$this->date 00:00' AND updated <= '$this->date 23:59' and issueType != Epic order by Key";

        $res = "$filter AND ($doneFilter) AND $date";

        return $res;
    }

    public function ImportIssues() {
        $total = 0;
        $this->logMessage = '';
        $this->message('Starting... [' . $this->date . ']');

        try {
            $filters = Filters::where('active', '=', 1)->get();
            foreach ($filters as $key => $filter) {
                $this->message("Checking $filter->description...");
                $json = $this->requestIssue( $this->encodeFilter($filter->filter, $filter->done_filter) );
                if (isset($json)) {
                    $this->processIssueList($json, $filter->id, 1);
                }
            }

            // update issues Story Points from Parent
            $this->message("\nChecking Parent Story Points...");
            $this->updateStoryPointsFromParent();

            $total = sizeof($this->cachedIssues);
            $this->message("\nDone. $total Issues Verified");

            $this->updateImportDateTime();
        } catch (\Throwable $th) {
            $this->message($th->getMessage());
            $this->message($th->getTraceAsString());
        }

        $this->sendNotification();

        return response()->json([
            'code' => 200,
            'message' => "Done. $total Issues Verified",
            'issues' => $this->cachedIssues
        ]);
    }

    function getIdentByLevel(int $level) {
        return ' ' . str_repeat('- ', $level);
    }

    function processIssueList($jsonList, int $filterId, int $level) {
        $issues = $jsonList["issues"];
        foreach ($issues as $key => $issue) {
            $key = $issue["key"];
            $fields = $issue["fields"];
            $this->message($this->getIdentByLevel($level) . $key);
            if (!array_key_exists($key, $this->cachedIssues)) {
                // check project
                $projectId = $this->projectsController->getProjectId($fields["project"]);

                // check issue
                $jsonIssue = json_decode(json_encode($issue), false);
                $issueId = $this->getIssueId($jsonIssue, $projectId, $filterId);

                // check personas
                $this->checkIssuePersona($issueId, $fields["assignee"], PersonaTypeEnum::ASSIGNEE);
                $this->checkIssuePersona($issueId, $fields["reporter"], PersonaTypeEnum::REPORTER);
                $this->checkIssuePersona($issueId, $fields["customfield_10131"], PersonaTypeEnum::REVISOR);
                $this->checkIssuePersonaCorresp($issueId, $fields["customfield_10412"]);

                // check parent issue
                if (property_exists($jsonIssue->fields, 'parent')) {
                    $parent = $jsonIssue->fields->parent->key;
                    $parentType = $jsonIssue->fields->parent->fields->issuetype->name;
                    if ($parentType != 'Epic') {
                        $this->message($this->getIdentByLevel($level + 1) . "Checking Parent Issue $parent ($parentType)");
                        $json = $this->requestIssue( "Key = $parent" );
                        if (isset($json)) {
                            $this->processIssueList($json, $filterId, $level + 1);
                        }
                    }
                }

                // check subtasks "subtasks": [],
                if (sizeof($jsonIssue->fields->subtasks) > 0) {
                    $parent = $jsonIssue->key;
                    $parentType = $jsonIssue->fields->issuetype->name;
                    $this->message($this->getIdentByLevel($level + 1) . "Checking SubTasks for $parent ($parentType)");
                    $json = $this->requestIssue( "Parent = $parent" );
                    if (isset($json)) {
                        $this->processIssueList($json, $filterId, $level + 1);
                    }
                }

                $this->cachedIssues[$key] = $issueId;
            } else {
                $this->message($this->getIdentByLevel($level) . '* Cache ' . $key);
            }
        }
    }

    function checkIssuePersona(int $issueId, $arr, PersonaTypeEnum $type) {
        if ($arr == NULL) {
            return null;
        }

        $personaId = $this->personasController->getPersonaId($arr);
        $issuePersona = IssuesPersonas::firstOrNew([
            'issue_id' => $issueId,
            'persona_id' => $personaId,
            'type' => $type
        ]);
        $issuePersona->issue_id = $issueId;
        $issuePersona->persona_id = $personaId;
        $issuePersona->type = $type;

        $issuePersona->save();
        return $issuePersona->id;
    }

    function checkIssuePersonaCorresp(int $issueId, $arr) {
        if ($arr != NULL) {
            foreach ($arr as $key => $corresp) {
                $this->checkIssuePersona($issueId, $corresp, PersonaTypeEnum::CORESPONSAVEL);
            }
        }
    }

    function jiraDateToDate($jiraDate) {
        // "2023-06-05T15:15:14.303-0300" -> "2023-06-05 15:15:14"
        if (isset($jiraDate)) {
            return substr($jiraDate, 0, 10) . " " . substr($jiraDate, 11, 8);
        }

        return null;
    }

    function getIssueURL(string $key) {
        return $this->baseURL . "/browse/$key";
    }

/*
project_id = fields->project->Key / Name
keyJira = key
summary = fields->summary
storyPoints = fields->customfield_10026
issueType = fields->issuetype->name
resolvedAt = fields->resolutiondate ("2023-06-05T15:15:14.303-0300")
resolution = fields->resolution->name
classe = fields->customfield_10144->value
tema = fields->customfield_10145->value
subTema = fields->customfield_10146->value
areaDemandante = fields->customfield_10035->value
parentKey = fields->parent->key
sprintId = fields->customfield_10020->id
sprintName = fields->customfield_10020->name
status = fields->status->name
- NEWS
priority = fields->priority->name
url = self
parentUrl = fields->parent->self
lastUpdated = fields->updated ("2023-06-05T15:15:14.303-0300")

----
corresp = fields->customfield_10412[]->displayName (id tb persona)
assignee = fields->assignee->displayName (id tb persona)
reporter = fields->reporter->displayName (id tb persona)
revisor = fields->customfield_10131->displayName (id tb persona)

"subtasks": [],
*/
    function getIssueId($json, int $projectId, int $filterId) {
        // cache first
        if (array_key_exists($json->key, $this->cachedIssues)) {
            return $this->cachedIssues[$json->key];
        }

        // load from database
        $issue = Issues::firstOrNew(['keyJira' => $json->key]);
        $issue->project_id = $projectId;
        $issue->keyJira = $json->key;
        $issue->summary = $json->fields->summary;
        $issue->issueType = $json->fields->issuetype->name;
        $issue->classe = (isset($json->fields->customfield_10144) ? $json->fields->customfield_10144->value : null);
        $issue->tema = (isset($json->fields->customfield_10145) ? $json->fields->customfield_10145->value : null);
        $issue->subTema = (isset($json->fields->customfield_10146) ? $json->fields->customfield_10146->value : null);
        $issue->areaDemandante = (isset($json->fields->customfield_10035) ? $json->fields->customfield_10035->value : null);
        $issue->status = $json->fields->status->name;
        $issue->priority = $json->fields->priority->name;
        $issue->url = $this->getIssueURL($json->key);
        $issue->lastUpdated = $this->jiraDateToDate($json->fields->updated);
        $issue->filterId = $filterId;

        $issue->storyPoints = (property_exists($json->fields, 'customfield_10026') ? $json->fields->customfield_10026 : 0);
        if ($issue->storyPoints == NULL) {
            $issue->storyPoints = 0;
        }

        if (isset($json->fields->customfield_10020)) {
            $idx = sizeof($json->fields->customfield_10020) - 1;
            $issue->sprintId = $json->fields->customfield_10020[$idx]->id;
            $issue->sprintName = $json->fields->customfield_10020[$idx]->name;
        }

        if (property_exists($json->fields, 'parent')) {
            $issue->parentKey = $json->fields->parent->key;
            $issue->parentUrl = $this->getIssueURL($issue->parentKey);

            if (!in_array($issue->parentKey, $this->parentIssues)) {
                $this->parentIssues[] = $issue->parentKey;
            }
        }

        if ($issue->issueType <> 'Subtarefa') {
            $issue->resolution = 'Done';
        }

        if (property_exists($json->fields, 'resolution') && isset($json->fields->resolution)) {
            $issue->resolution = $json->fields->resolution->name;
            $issue->resolvedAt = $this->jiraDateToDate($json->fields->resolutiondate);
        }

        $issue->save();

        $this->cachedIssues[$json->key] = $issue->id;

        return $issue->id;
    }

    function updateStoryPointsFromParent() {
        foreach ($this->parentIssues as $key => $keyId) {
            $this->message(" - $keyId");
            $parent = Issues::where('keyJira', '=', $keyId)->first();

            if (isset($parent) && ($parent->id > 0)) {
                $affected = Issues::where('parentKey', '=', $keyId)->update(
                    array('storyPoints' => $parent->storyPoints)
                );

                $this->message(" -- QTD: $affected");
            }
        }
    }

    function sendNotification() {
        if ($this->rocket->isConfigurated()) {
            $status = $this->rocket->sendMessage($this->logMessage);
            $this->message($status["message"]);
        }
    }

    function updateImportDateTime() {
        $config = Config::find(1);
        $config->value = $this->date;
        $config->save();

        $config = Config::find(2);
        $config->value = date('H:i:s', time());
        $config->save();
    }

}
