<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>
<?$APPLICATION->AddHeadScript($templateFolder . "/jquery.min.js");?>
<?$APPLICATION->AddHeadScript($templateFolder . "/map.js");?>
<?$APPLICATION->AddHeadScript($templateFolder . "/chosen.jquery.min.js");?>
<?$APPLICATION->AddHeadScript($templateFolder . "/chosen.jquery.min.js");?>
<?$APPLICATION->SetAdditionalCSS($templateFolder . "/chosen.min.css");?>

<?if (!empty($arResult['CITY_ID'])) {?>
    <script src="https://api-maps.yandex.ru/2.1/?apikey=<?=$arParams['YANDEX_API_KEY']?>&lang=ru_RU" type="text/javascript">
    </script>
    <script>
        $( document ).ready(function(){
            DevelopxTK_ = new DevelopxTK(<?=CUtil::PhpToJSObject($arResult['POINTS'], false, true)?>, <?=CUtil::PhpToJSObject($arResult['PRICES'], false, true)?>, '<?=$arResult['CURRENT_LOCATION']['LOC_NAME']?>', '<?=$templateFolder?>');
        });
    </script>
<?}?>

<div class="map-box">
    <?if (!empty($arResult['CITY_ID'])) {?>
        <div class="row">
            <div class="col-md-6">
                <div class="title"><?=Loc::getMessage('DX_TX_TITLE')?><a href="#" class="header__city cityNameJs" data-toggle="modal" data-target="#cityModal"><?=$arResult['CURRENT_LOCATION']['LOC_NAME']?></a></div>
            </div>
            <div class="col-md-6 text-right">
                <div class="map-nav nav justify-content-end">
                    <a href="#checkoutMap" class="nav-link active navLinkJs" ><?=Loc::getMessage('DX_TX_MAP')?></a>
                    <a href="#checkoutList" class="nav-link navLinkJs"><?=Loc::getMessage('DX_TX_LIST')?></a>
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
                                <th scope="col" class="w-280"><?=Loc::getMessage('DX_TX_LIST_ADR')?></th>
                                <th scope="col"><?=Loc::getMessage('DX_TX_LIST_PRICE')?></th>
                                <th scope="col"><?=Loc::getMessage('DX_TX_LIST_TIME')?></th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?}else{?>
        <div class="row">
            <div class="col-md-6">
                <div class="title">
                    <a href="#" class="header__city cityNameJs" data-toggle="modal" data-target="#cityModal">
                        <?=Loc::getMessage('DX_TX_TITLE_SELECT')?>
                    </a>
                </div>
            </div>
        </div>
    <?}?>

    <div class="overflow"></div>
    <div class="map-modal" id="cityModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title"><?=Loc::getMessage('DX_TX_TITLE_SELECT')?></div>
                    <a class="modal-close modalCloseJs" href="#" data-dismiss="modal">
                        <svg fill="none" viewBox="0 0 15 15" id="close" xmlns="http://www.w3.org/2000/svg"><path d="M6 7.5l-6 6L1.5 15l6-6 6 6 1.5-1.5-6-6 6-6L13.5 0l-6 6-6-6L0 1.5l6 6z" fill="#93979B"></path></svg>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <select id="cityChoseJs" class="form-city__city-wrap" data-placeholder="<?=Loc::getMessage('DX_TX_TITLE_SELECT')?>">
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

<script>
    $( document ).ready(function() {
        $('#cityChoseJs').chosen();
        $("#cityChoseJs").chosen({no_results_text: "<?=Loc::getMessage('DX_TX_FIND_NO')?>"}).change(function () {
            window.location.href = '?CITY_ID=' + $('#cityChoseJs').val();
        });
    });
</script>

