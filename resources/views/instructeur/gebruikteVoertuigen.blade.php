<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gebruikte voertuigen</title>

    @vite(['resources/scss/instructeur/gebruikteVoertuigen.scss', 'resources/scss/instructeur/global.scss'])
</head>
<body>
    <style>
        img {
            width: 50px;
            height: 50px;
        }

    </style>

    <div id="container">
        <h1>Door instructeur gebruikte voertuigen</h1>

        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @elseif (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
        @endif

        <div id="instructeurList">
            <p><span>Naam:</span> {{$instructeurs->voornaam}} {{$instructeurs->tussenvoegsel}} {{$instructeurs->achternaam}}</p>
            <p><span>Datum in dienst:</span> {{$instructeurs->datumInDienst}}</p>
            <p><span>Aantal sterren:</span> {{$instructeurs->aantalSterren}}</p>
            <a href="{{route('instructeur.beschikbareVoertuigen', [$instructeurs->id])}}">Toevoegen voertuig</a>
            <a href="{{route('instructeur.index')}}">Terug naar instructeur lijst</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Type voertuig</th>
                    <th>Type</th>
                    <th>Kenteken</th>
                    <th>Bouwjaar</th>
                    <th>Brandstof</th>
                    <th>Rijbewijscategorie</th>
                    <th>Wijzigen</th>
                    <th>Verwijderen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($voertuigGegevens as $voertuig)
                <tr>
                    <td>{{$voertuig->typeVoertuig}}</td>
                    <td>{{$voertuig->type}}</td>
                    <td>{{$voertuig->kenteken}}</td>
                    <td>{{$voertuig->bouwjaar}}</td>
                    <td>{{$voertuig->brandstof}}</td>
                    <td>{{$voertuig->rijbewijsCategorie}}</td>
                    <td><a href="{{route('instructeur.wijzigenVoertuigen', [$instructeurs->id, $voertuig->id])}}"><img src="/img/Edit-icon.png" alt="wijzigenVoertuig.png"></a></td>
                    <td><a href="{{route('instructeur.unassign', [$instructeurs->id, $voertuig->id])}}"><img src="/img/Delete-icon.png" alt="verwijderVoertuig.png"></a></td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
