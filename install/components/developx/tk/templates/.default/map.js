$( document ).ready(function() {

    if (window.DevelopxTK)
        return;

    window.DevelopxTK = function (points, prices, cityName, path) {
        this.points = points;
        this.prices = prices;
        this.path = path;
        this.cityName = cityName;
        this.center = false;
        this.Y_map = false;
        this.placeMarks = {};
        this.selectedPoint = false;
        this.initEvents();
        this.ymapsBidner();
    };

    window.DevelopxTK.prototype = {
        ymapsBidner: function(){
            var $this = this;
            ymaps.ready(function (){
                $this.initMap();
            });
        },
        initMap: function(){
            var $this = this;
            ymaps.geocode($this.cityName , {
                results: 1
            }).then(function (res) {
                console.log(res);
                var firstGeoObject = res.geoObjects.get(0);
                $this.center = firstGeoObject.geometry.getCoordinates();
                $this.Y_map = new ymaps.Map("checkoutMap", {
                    zoom: 10,
                    center: $this.center
                }, {suppressMapOpenBlock: true});
                $this.Y_addPoints();
                $this.L_addPoints();
            });
        },
        Y_addPoints: function(){
            var $this = this;
            for(var i in $this.points){
                var point = $this.points[i];
                if (typeof $this.prices[point['TK']] !== 'undefined') {

                    var id = point['ID'];
                    var baloonHTML = "";
                    baloonHTML += "<div class='pointPopup' class='" + point['TK'] + "'>";
                    baloonHTML += "<div class='pointImg'><img src='" + $this.path + "/icons/" + point['TK'] + ".png'></div>";
                    baloonHTML += "<div class='pointTitle'>" + $this.prices[point['TK']]['TITLE'] + "</div>";
                    baloonHTML += "<div class='pointPrice'>" + $this.prices[point['TK']]['PRICE'] + " ?</div>";
                    baloonHTML += "<div class='pointTime'>" + $this.prices[point['TK']]['TIME_FORMAT'] + "</div>"
                    baloonHTML += "<div class='pointAdress'>" + point['ADR'] + "</div>";
                    if (typeof point['WORK_TIME'] != 'undefined' && point['WORK_TIME'] != '-') {
                        baloonHTML += "<div class='pointWorkTime'>" + point['WORK_TIME'] + "</div>";
                    }
                    $this.placeMarks[id] = new ymaps.Placemark(
                        [point['GPS_N'], point['GPS_S']],
                        {
                            balloonContent: baloonHTML
                        },
                        {
                            iconColor: '#159ebb',
                            preset: 'islands#circleIcon',
                        }
                    );
                    $this.Y_map.geoObjects.add($this.placeMarks[id]);
                    $this.placeMarks[id].link = i;
                }
            }
        },
        L_addPoints: function(){
            var $this = this;
            for(var i in $this.points){
                var point = $this.points[i];
                if (typeof $this.prices[point['TK']] !== 'undefined') {
                    var id = point['ID'];
                    var baloonHTML = '<tr data-id="' + point['ID'] + '">';
                    baloonHTML += '<td>';
                    baloonHTML += "<div class='pointImg'><img src='" + $this.path + "/icons/" + point['TK'] + ".png'></div>";
                    baloonHTML += '</td>';
                    baloonHTML += '<td>';
                    baloonHTML += '<span class="muted">' + point['ADR'] + '</span>';
                    if (point['PHONE'] != '-') {
                        baloonHTML += '<span class="phone">' + point['PHONE'] + '</span>';
                    }
                    baloonHTML += '</td>';
                    baloonHTML += '<td>' + $this.prices[point['TK']]['PRICE'] + ' ?</td>';
                    baloonHTML += '<td class="time">' + $this.prices[point['TK']]['TIME_FORMAT'] + '</td>';
                    baloonHTML += '</tr>';
                    $("#checkoutList tbody").prepend(baloonHTML);
                }
            }
        },
        Y_allPoint: function(){
            var $this = this;
            $this.Y_map.setCenter($this.center);
            $this.Y_map.setZoom(10);
        },
        initEvents: function () {
            var $this = this;
            $('body').on('click', '.selectOtherPointsJs', function () {
                $this.Y_allPoint();
                $('#checkoutMap .selected-block').slideUp();
                $('.prop-POINT_ID input').val('');
                return false;
            });
            $('.navLinkJs').click(function () {
                 $('.navLinkJs').removeClass('active');
                 $(this).addClass('active');
                 $('.mapContentJs').removeClass('selected');
                 $($(this).attr('href')).addClass('selected');
                 return false;
            });

            $('.cityNameJs').click(function () {
                $('body').addClass('map-modal-active');
                return false;
            });

            $("#cityChoseJs").chosen({no_results_text: "Ничего не найдено"}).change(function () {
                window.location.href = '?CITY_ID=' + $('#cityChoseJs').val();
            });

            $('.map-modal .modalCloseJs').click(function () {
                $('body').removeClass('map-modal-active');
                return false;
            });
        }
    }
});