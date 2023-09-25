<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    private $cachedProjects;
    private $baseURL;

    public function __construct() {
        $this->cachedProjects = null;
        $this->baseURL = env('JIRA_URL');
    }

    function getCachedValues() {
        $arr = array();
        $projects = Projects::all();

        foreach ($projects as $key => $project) {
            $arr[$project["project_key"]] = $project->id;
        }

        return $arr;
    }

    public function getProjectId($projectObject) {
/*
    "project": {
        "self": "https://kinghost.atlassian.net/rest/api/3/project/10007",
        "id": "10007",
        "key": "AFL",
        "name": "Afiliados",
        "projectTypeKey": "software",
        "simplified": false,
        "avatarUrls": {
            "48x48": "https://kinghost.atlassian.net/rest/api/3/universal_avatar/view/type/project/avatar/10410",
            "24x24": "https://kinghost.atlassian.net/rest/api/3/universal_avatar/view/type/project/avatar/10410?size=small",
            "16x16": "https://kinghost.atlassian.net/rest/api/3/universal_avatar/view/type/project/avatar/10410?size=xsmall",
            "32x32": "https://kinghost.atlassian.net/rest/api/3/universal_avatar/view/type/project/avatar/10410?size=medium"
        }
    }
*/
        $json = json_decode(json_encode($projectObject), false);

        if (!isset($this->cachedProjects)) {
            $this->cachedProjects = $this->getCachedValues();
        }

        if (array_key_exists($json->key, $this->cachedProjects)) {
            return $this->cachedProjects[$json->key];
        }

        $key = $json->key;
        // if not found, create
        $proj = new Projects;
        $proj->project_key = $key;
        $proj->displayName = $json->name;
        $proj->code = $json->id;
        $proj->url = $this->baseURL . "/jira/software/c/projects/$key/issues"; //$json->self;
        $proj->avatar = $projectObject["avatarUrls"]["48x48"];
        $proj->save();

        $this->cachedProjects[$json->key] = $proj->id;
        return $proj->id;
    }

}
