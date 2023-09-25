<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonasRequest;
use App\Http\Requests\UpdatePersonasRequest;
use App\Models\Personas;

use function PHPUnit\Framework\isNull;

class PersonasController extends Controller
{
    private $cachedPersonas;

    public function __construct() {
        $this->cachedPersonas = null;
    }

    function getCachedValues() {
        $arr = array();
        $personas = Personas::all();

        foreach ($personas as $key => $persona) {
            $arr[$persona["displayName"]] = $persona->id;
        }

        return $arr;
    }

    public function getPersonaId($arr) {
        $json = json_decode(json_encode($arr), false);

        if ($this->cachedPersonas == NULL) {
            $this->cachedPersonas = $this->getCachedValues();
        }

        if (array_key_exists($json->displayName, $this->cachedPersonas)) {
            return $this->cachedPersonas[$json->displayName];
        }

        // if not found, create
        $persona = new Personas();
        $persona->displayName = $json->displayName;
        $persona->accountId = $json->accountId;
        $persona->active = $json->active;
        $persona->avatar = $arr["avatarUrls"]["48x48"];
        $persona->save();

        $this->cachedPersonas[$json->displayName] = $persona->id;
        return $persona->id;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonasRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Personas $personas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Personas $personas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonasRequest $request, Personas $personas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Personas $personas)
    {
        //
    }
}
