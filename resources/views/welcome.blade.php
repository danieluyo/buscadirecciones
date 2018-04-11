<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,400,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Roboto', sans-serif;
                font-weight: 100;
                padding: 0;
                height: 100vh;
                margin: 0;
            }

            label{
                display: block;
                margin: auto;
            }
            input[type=text]{
                width: 100%;
                padding: 3px 5px;
                font-size: 16px;
                margin-top: 30px;
                height: 35px;
            }
            #resultado{
                width: 100%;
                margin: auto;
                position: relative;
                text-align: center;
                margin-top: 10px;
                margin-bottom: 10px;
                background-color: #dedede;
                padding: 10px 10px;
                color: #000;
            }
            #resultadogeo{
                width: 100%;
                margin: auto;
                position: relative;
                text-align: center;
                margin-top: 10px;
                margin-bottom: 20px;
                background-color: #dedede;
                padding: 10px 10px;
                color: #000;
            }

            #map {
                position: relative;
            }

            .medium-50{
                float: left;
                position: relative;
                width: 50%;
                height: 100vh;
            }
            .content{
                text-align: center;
                position: relative;
                vertical-align: middle;
                padding: 15px 30px;
            }
        </style>
    </head>
    <body>

    <div class="full medium-50">
        <div class="content">

            <form style="width: 100%;">
                @csrf
                <label for="">Direccion</label>
                <input type="text" name="direccion" autocomplete="off" autofocus>
                <button type="button" id="btn" style="width: 200px; padding: 10px 10px; background-color: #748aff; margin-top: 15px; border-radius:2px;font-size: 16px; ">Buscar</button>
            </form>

            <div id="resultado"></div>
            <div id="resultadogeo"></div>
        </div>
    </div>
    <div class="full medium-50" id="map">
    </div>
    </body>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('API_GOOGLE_MAPS') }}&callback="></script>
    <script>
            var uluru = {lat: 19.432608, lng: -99.133209};
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 4,
                center: uluru
            });
            var marker = new google.maps.Marker({
                draggable: true,
                position: uluru,
                map: map
            });


            google.maps.event.addListener(marker, 'dragend', function (event) {
                $('#resultadogeo').empty().append(
                    '<table style="margin: auto;"><tr><td><input type="text" value="'+event.latLng.lat() +'"></td><td><input type="text" value="'+event.latLng.lng()+'"></td></tr></table>'
                );
            });

            function post() {
                var $pos = $('#resultado');
                $pos.empty().text('Buscando....');

                $('#resultadogeo').empty();

                axios.post('/', {
                    direccion: $('input[name=direccion]').val()
                })
                    .then(function (response) {

                        $pos.empty();

                        if(response.data.status === 'OK'){
                            var data = response.data;
                            var objs = data.results;
                            for (i = 0; i < objs.length; i++) {
                                var obj = objs[i];
                                var loc = obj.geometry.location;
                                var lati = loc.lat;
                                var lngi = loc.lng;
                                var add = obj.formatted_address;
                                var tpl = '<p>'+add+'</p>';
                                var tab = '<table style="margin: auto;"><tr><td><input type="text" value="'+lati+'"></td><td><input type="text" value="'+lngi+'"></td></tr></table>';
                                    $pos.append(tpl);
                                    $('#resultadogeo').append(tab);

                                var latlng = new google.maps.LatLng(lati, lngi);
                                    marker.setPosition(latlng);
                                    marker.setMap(map);
                                    map.setCenter(latlng);
                                    map.setZoom(17);
                            }

                        }else{
                            $pos.empty().text(response.data.status);
                        }

                    })
                    .catch(function (error) {
                        console.log(error);
                        $pos.empty().text('Error, verifica la informacion agregada');
                    });

            }

            $(document).on('click', '#btn', function(){
                post();
            });

            $("form").submit(function (e) {
                e.preventDefault();
                post();
            });
    </script>

</html>
