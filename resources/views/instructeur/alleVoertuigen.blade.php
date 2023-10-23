<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alle voertuigen</title>

    @vite(['resources/scss/instructeur/alleVoertuigen.scss', 'resources/css/instructeur/global.css'])
</head>
<body>
    <style>
        img {
            width: 50px;
            height: 50px;
        }

    </style>

    <div id="container">
        <h1>Alle voertuigen</h1>

        <a href="{{route('instructeur.index')}}">Terug naar instructeur lijst</a>

        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        @if (isset($error))
        <div class="alert alert-error">
            {{ $error }}
        </div>
        <div id="redirect">
            <p>Redirecting in <span id="countdown">3</span> seconds.</p>
        </div>
        <script>
            let countdown = 3;

            function updateCountdown() {
                countdown -= 1;
                document.getElementById('countdown').textContent = countdown;

                if (countdown <= 0) {

                    window.location.href = "{{ route('instructeur.index') }}";
                } else {
                    setTimeout(updateCountdown, 1000);
                }
            }

            setTimeout(updateCountdown, 1000);

        </script>
        @endif

        @if (isset($voertuigGegevens) && !$voertuigGegevens->isEmpty())
        <table>
            <thead>
                <tr>
                    <th>Type voertuig</th>
                    <th>Type</th>
                    <th>Kenteken</th>
                    <th>Bouwjaar</th>
                    <th>Brandstof</th>
                    <th>Rijbewijscategorie</th>
                    <th>Instructeur naam</th>
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
                    <td>
                        @if ($voertuig->instructeursId)
                        {{$voertuig->voornaam}} {{$voertuig->tussenvoegsel}} {{$voertuig->achternaam}}
                        @endif
                    </td>
                    <td><a href="{{route('instructeur.delete', [$voertuig->id])}}"><img src="/img/Delete-icon.png" alt="verwijderVoertuig.png"></a></td>
                    @endforeach
                </tr>
            </tbody>
        </table>
        @endif
    </div>
</body>

</html>
