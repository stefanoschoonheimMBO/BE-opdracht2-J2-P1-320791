<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instructeur;         // Import models
use App\Models\Voertuig;
use App\Models\TypeVoertuig;
use App\Models\VoertuigInstructeur;
use Illuminate\Support\Facades\DB;

class InstructeurController extends Controller
{
    public function index()
    {
        $aantalInstructeurs = Instructeur::distinct()->count('id');

        $instructeurList = DB::table('instructeurs')
            ->select('id', 'voornaam', 'tussenvoegsel', 'achternaam', 'mobiel', 'datumInDienst', 'aantalSterren')
            ->orderBy('aantalSterren', 'desc')
            ->get();

        return view('instructeur.index', [
            'instructeurList' => $instructeurList,
            'aantalInstructeurs' => $aantalInstructeurs,
        ]);
    }

    public function gebruikteVoertuigen(Instructeur $instructeur)
    {
        $instructeurId = $instructeur->id;

        $voertuigGegevens = Voertuig::select('voertuigs.id', 'voertuigs.type', 'voertuigs.kenteken', 'voertuigs.bouwjaar', 'voertuigs.brandstof', 'typeVoertuigs.typeVoertuig', 'typeVoertuigs.rijbewijsCategorie')
            ->join('voertuigInstructeurs', 'voertuigs.id', '=', 'voertuigInstructeurs.voertuigsId')
            ->join('instructeurs', 'voertuigInstructeurs.instructeursId', '=', 'instructeurs.id')
            ->join('typeVoertuigs', 'voertuigs.typeVoertuigsId', '=', 'typeVoertuigs.id')
            ->where('instructeurs.id', $instructeurId)
            ->orderBy('typeVoertuigs.rijbewijsCategorie', 'asc')
            ->get();

        return view('instructeur.gebruikteVoertuigen', ['instructeurs' => $instructeur, 'voertuigGegevens' => $voertuigGegevens]);
    }

    public function wijzigenVoertuigen(Instructeur $instructeur, $voertuig)
    {
        $instructeurList = Instructeur::all();
        $typeVoertuigList = TypeVoertuig::select('id', 'typeVoertuig')->get();

        $voertuigGegevens = DB::table('voertuigs')
            ->select('voertuigInstructeurs.*', 'voertuigs.id', 'voertuigs.type', 'voertuigs.kenteken', 'voertuigs.bouwjaar', 'voertuigs.brandstof', 'voertuigs.typeVoertuigsId', 'typeVoertuigs.rijbewijscategorie', 'typeVoertuigs.typeVoertuig')
            ->leftJoin('voertuigInstructeurs', 'voertuigs.id', '=', 'voertuigInstructeurs.voertuigsId')
            ->join('typeVoertuigs', 'voertuigs.typeVoertuigsId', '=', 'typeVoertuigs.id')
            ->where('voertuigs.id', $voertuig)
            ->get();

        return view('instructeur.wijzigenVoertuigen', [
            'instructeurs' => $instructeur,
            'voertuigGegevens' => $voertuigGegevens,
            'instructeurList' => $instructeurList,
            'typeVoertuigList' => $typeVoertuigList,
            'voertuigId' => $voertuig,
        ]);
    }

    public function update(Request $request, Instructeur $instructeur, Voertuig $voertuig)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'instructeur' => 'required|exists:instructeurs,id',
            'typeVoertuig' => 'required|exists:typeVoertuigs,id',
            'type' => 'required',
            'bouwjaar' => 'required|date',
            'brandstof' => 'required|in:Diesel,Benzine,Elektrisch',
            'kenteken' => 'required|max:50',
        ]);

        $voertuig = Voertuig::findOrFail($voertuig->id);

        $voertuig->type = $validatedData['type'];
        $voertuig->bouwjaar = $validatedData['bouwjaar'];
        $voertuig->brandstof = $validatedData['brandstof'];
        $voertuig->kenteken = $validatedData['kenteken'];
        $voertuig->typeVoertuigsId = $validatedData['typeVoertuig'];

        $voertuig->save();

        if ($request->has('instructeur')) {
            $instructeurId = $request->input('instructeur');
            $voertuigId = $request->input('voertuig');
            $voertuigInstructeur = VoertuigInstructeur::updateOrCreate(
                ['voertuigsId' => $voertuigId],
                ['instructeursId' => $instructeurId]
            );
        }

        return redirect()->route('instructeur.gebruikteVoertuigen', ['instructeur' => $instructeur])
            ->with('success', 'Voertuig data succesvol geupdate.');
    }

    public function beschikbareVoertuigen(Instructeur $instructeur)
    {
        $unassignedVehicles = Voertuig::select('voertuigs.id', 'voertuigs.type', 'voertuigs.kenteken', 'voertuigs.bouwjaar', 'voertuigs.brandstof', 'typeVoertuigs.typeVoertuig', 'typeVoertuigs.rijbewijsCategorie')
            ->join('voertuigInstructeurs', 'voertuigs.id', '=', 'voertuigInstructeurs.voertuigsId', 'left')
            ->join('typeVoertuigs', 'voertuigs.typeVoertuigsId', '=', 'typeVoertuigs.id')
            ->whereNull('voertuigInstructeurs.voertuigsId')
            ->orderBy('voertuigs.id', 'asc')
            ->get();

        return view('instructeur.beschikbareVoertuigen', [
            'instructeurs' => $instructeur,
            'unassignedVehicles' => $unassignedVehicles
        ]);
    }

    public function addVehicle(Instructeur $instructeur, Voertuig $voertuig)
    {
        $instructeurId = $instructeur->id;
        $voertuigId = $voertuig->id;
        $datumToekenning = date('y-m-d');
        $createdAt = date('y-m-d h:i:s');
        $updatedAt = date('y-m-d h:i:s');

        $vehicleData = VoertuigInstructeur::insert(array(
            'voertuigsId' => $voertuigId,
            'instructeursId' => $instructeurId,
            'datumToekenning' => $datumToekenning,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt
        ));

        return redirect()->route('instructeur.gebruikteVoertuigen', ['instructeur' => $instructeur])
            ->with('success', 'Voertuig succesvol toegevoegd.');
    }

    public function unassign(Instructeur $instructeur, Voertuig $voertuig)
    {
        $voertuigInstructeur = VoertuigInstructeur::where('instructeursId', $instructeur->id)
            ->where('voertuigsId', $voertuig->id)
            ->first();

        if ($voertuigInstructeur) {
            $voertuigInstructeur->delete();

            return redirect()->route('instructeur.gebruikteVoertuigen', ['instructeur' => $instructeur])
                ->with('success', 'Voertuig is succesvol ontkoppeld van de instructeur.');
        } else {
            return redirect()->route('instructeur.gebruikteVoertuigen', ['instructeur' => $instructeur])
                ->with('error', 'Error, dit voertuig is niet toegewezen aan de instructeur.');
        }
    }

    public function alleVoertuigen()
    {
        $voertuigGegevens = Voertuig::select('voertuigs.id', 'voertuigs.type', 'voertuigs.kenteken', 'voertuigs.bouwjaar', 'voertuigs.brandstof', 'typeVoertuigs.typeVoertuig', 'typeVoertuigs.rijbewijsCategorie', 'instructeurs.id as instructeursId', 'instructeurs.voornaam', 'instructeurs.tussenvoegsel', 'instructeurs.achternaam')
            ->leftJoin('voertuigInstructeurs', 'voertuigs.id', '=', 'voertuigInstructeurs.voertuigsId')
            ->leftJoin('instructeurs', 'voertuigInstructeurs.instructeursId', '=', 'instructeurs.id')
            ->join('typeVoertuigs', 'voertuigs.typeVoertuigsId', '=', 'typeVoertuigs.id')
            ->orderBy('voertuigs.bouwjaar', 'desc')
            ->orderBy('instructeurs.achternaam', 'asc')
            ->get();

        if ($voertuigGegevens->isEmpty()) {
            $error = 'Er zijn geen voertuigen beschikbaar op dit moment.';
            return view('instructeur.alleVoertuigen', ['voertuigGegevens' => $voertuigGegevens, 'error' => $error]);
        }

        return view('instructeur.alleVoertuigen', ['voertuigGegevens' => $voertuigGegevens]);
    }

    public function delete(Voertuig $voertuig)
    {
        $voertuig->delete();

        return redirect()->route('instructeur.alleVoertuigen', ['voertuig' => $voertuig])
            ->with('success', 'Voertuig is succesvol verwijderd.');
    }
}
