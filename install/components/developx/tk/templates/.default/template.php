<?$APPLICATION->AddHeadScript($templateFolder."/jquery.min.js");?>
<?$APPLICATION->AddHeadScript($templateFolder."/map.js");?>
<?$APPLICATION->AddHeadScript($templateFolder."/chosen.jquery.min.js");?>
<?$APPLICATION->AddHeadScript($templateFolder."/chosen.jquery.min.js");?>
<?$APPLICATION->SetAdditionalCSS($templateFolder."/chosen.min.css");?>

<script src="https://api-maps.yandex.ru/2.1/?apikey=<?=$arParams['YANDEX_API_KEY']?>&lang=ru_RU" type="text/javascript">
</script>
<script>
    $( document ).ready(function(){
        DevelopxTK_ = new DevelopxTK(<?=CUtil::PhpToJSObject($arResult['POINTS'], false, true)?>, <?=CUtil::PhpToJSObject($arResult['PRICES'], false, true)?>, '<?=$arResult['CURRENT_LOCATION']['LOC_NAME']?>', '<?=$templateFolder?>');
    });
</script>

<div class="map-box">
    <div class="row">
        <div class="col-md-6">
            <div class="title">Пункты самовывоза в г.<a href="#" class="header__city cityNameJs" data-toggle="modal" data-target="#cityModal"><?=$arResult['CURRENT_LOCATION']['LOC_NAME']?></a></div>
        </div>
        <div class="col-md-6 text-right">
            <div class="map-nav nav justify-content-end">
                <a href="#checkoutMap" class="nav-link active navLinkJs" >На карте</a>
                <a href="#checkoutList" class="nav-link navLinkJs">Списком</a>
            </div>
        </div>

        <div class="tab-content col-md-12">
            <div class="map-content mapContentJs selected" id="checkoutMap">
            </div>

            <div class="map-content mapContentJs" id="checkoutList">
                <div class="table-responsive mb-60">
                    <table class="table product-delivery__table">
                        <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col" class="w-280">Адрес</th>
                            <th scope="col">Стоимость</th>
                            <th scope="col">Сроки </th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow"></div>
    <div class="map-modal" id="cityModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Выбор города</div>
                    <a class="modal-close modalCloseJs" href="#" data-dismiss="modal">
                        <svg fill="none" viewBox="0 0 15 15" id="close" xmlns="http://www.w3.org/2000/svg"><path d="M6 7.5l-6 6L1.5 15l6-6 6 6 1.5-1.5-6-6 6-6L13.5 0l-6 6-6-6L0 1.5l6 6z" fill="#93979B"></path></svg>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <select id="cityChoseJs" class="form-city__city-wrap" data-placeholder="Выберите город">
                                    <option></option>
                                    <?foreach ($arResult['LOCATIONS'] as $loc){?>
                                        <option value="<?=$loc['ID']?>"><?=$loc['LOC_NAME']?></option>
                                    <?}?>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>



