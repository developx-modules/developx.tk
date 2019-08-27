<?
if ($USER->IsAdmin()):

    $moduleName = 'developx.tk';

    CJSCore::Init('jquery');
    $APPLICATION->AddHeadScript('/bitrix/js/'.$moduleName.'/main.js');

    $arProps = [
        'DAYS_DOP',
        'PRICE_DOP',
        'DEF_WEIGHT',
        'DEF_LENGTH',
        'DEF_WIDE',
        'DEF_HEIGHT',
        'PEC_ACTIVE',
        'PEC_KEY',
        'CDEK_ACTIVE',
        'DL_ACTIVE',
        'DL_KEY',
        'JDE_ACTIVE',
        'ENERGY_ACTIVE',
        'ENERGY_API_KEY',
    ];

    if ($_POST['Update'] && check_bitrix_sessid()) {
        foreach ($arProps as $prop){
            COption::SetOptionString($moduleName, $prop, $_POST[$prop]);
        }
    }

    foreach ($arProps as $prop){
        $arFields[$prop] = COption::GetOptionString($moduleName, $prop);
    }

    if (empty($arFields['DAYS_DOP'])){
        $arFields['DAYS_DOP'] = 0;
    }
    if (empty($arFields['PRICE_DOP'])){
        $arFields['PRICE_DOP'] = 0;
    }

    if (empty($arFields['DEF_WEIGHT'])){
        $arFields['DEF_WEIGHT'] = 1;
    }

    if (empty($arFields['DEF_LENGTH'])){
        $arFields['DEF_LENGTH'] = 0.2;
    }

    if (empty($arFields['DEF_WIDE'])){
        $arFields['DEF_WIDE'] = 0.2;
    }

    if (empty($arFields['DEF_HEIGHT'])){
        $arFields['DEF_HEIGHT'] = 0.2;
    }

    $aTabs = array(
        array("DIV" => "edit1", "TAB" => "Основые настройки", "ICON" => "main_user_edit", "TITLE" => "Основые настройки"),
        array("DIV" => "edit2", "TAB" => "Настройка ТК", "ICON" => "main_user_edit", "TITLE" => "Настройка транспортных компаний"),
        array("DIV" => "edit3", "TAB" => "Кэширование", "ICON" => "main_user_edit", "TITLE" => "Кэширование"),
        array("DIV" => "edit4", "TAB" => "Загрузка пунктов ТК", "ICON" => "main_user_edit", "TITLE" => "Загрузка пунктов ТК"),
    );
    $tabControl = new CAdminTabControl("tabControl", $aTabs);

    $tabControl->Begin();?>
    <form name="kombox_filter_options" method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($mid)?>&amp;lang=<?echo LANG?>&amp;mid_menu=1">
        <?=bitrix_sessid_post();?>
        <?$tabControl->BeginNextTab();?>
        <tr class="heading">
            <td colspan="2">Настройки</td>
        </tr>

        <tr>
            <td width="40%">Город-отправитель</td>
            <?
            $cityDef = COption::GetOptionString('sale','location',false);
            $arLocs = CSaleLocation::GetByID($cityDef, LANGUAGE_ID);
            ?>
            <td width="60%">
                <input disabled type="text" name="" value="<?=$arLocs["CITY_NAME_LANG"]?>" size="30" maxlength="100">
                <br>
                <div class="pop-text">Город-отправитель выставляется в <a href="/bitrix/admin/settings.php?lang=ru&amp;mid=sale" target="_blank">настройках Интернет-магазина</a> в закладке "Адрес магазина" -&gt; Местоположение магазина.</div>

            </td>
        </tr>

        <tr>
            <td width="50%">Увеличить срок доставки на (дн.)<a name="opt_termInc"></a>
            </td>
            <td width="50%"><input type="text" size="1" maxlength="255" value="<?=$arFields["DAYS_DOP"]?>" name="DAYS_DOP">
            </td>
        </tr>
        <tr>
            <td width="50%">Увеличить стоимость доставка на (руб.)<a name="opt_termInc"></a>
            </td>
            <td width="50%"><input type="text" size="5" maxlength="255" value="<?=$arFields["PRICE_DOP"]?>" name="PRICE_DOP">
            </td>
        </tr>

        <tr class="heading">
            <td colspan="2">Стандартные габариты товара</td>
        </tr>
        <tr>
            <td width="40%">Вес, кг</td>
            <td width="60%"><input type="text" name="DEF_WEIGHT" value="<?=$arFields["DEF_WEIGHT"]?>" size="30" maxlength="100"></td>
        </tr>
        <tr>
            <td width="40%">Длина, м</td>
            <td width="60%"><input type="text" name="DEF_LENGTH" value="<?=$arFields["DEF_LENGTH"]?>" size="30" maxlength="100"></td>
        </tr>
        <tr>
            <td width="40%">Ширина, м</td>
            <td width="60%"><input type="text" name="DEF_WIDE" value="<?=$arFields["DEF_WIDE"]?>" size="30" maxlength="100"></td>
        </tr>
        <tr>
            <td width="40%">Высота, м</td>
            <td width="60%"><input type="text" name="DEF_HEIGHT" value="<?=$arFields["DEF_HEIGHT"]?>" size="30" maxlength="100"></td>
        </tr>


        <?$tabControl->BeginNextTab();?>
        <tr class="heading">
            <td colspan="2">ПЭК</td>
        </tr>
        <tr>
            <td width="50%">Активен</td>
            <td width="50%"><input type="checkbox" name="PEC_ACTIVE" value="Y"<?if($arFields["PEC_ACTIVE"] == "Y") echo " checked"?>></td>
        </tr>
        <tr>
            <td width="50%">ApiKey</td>
            <td width="50%">
                <input type="text" name="PEC_KEY" value="<?=$arFields["PEC_KEY"]?>" size="30" maxlength="100">
                <a href="https://pecom.ru/business/developers/api/" target="_blank">Получить ApiKey ПЭК</a>
            </td>
        </tr>

        <tr class="heading">
            <td colspan="2">СДЕК</td>
        </tr>
        <tr>
            <td width="50%">Активен</td>
            <td width="50%"><input type="checkbox" name="CDEK_ACTIVE" value="Y"<?if($arFields["CDEK_ACTIVE"] == "Y") echo " checked"?>></td>
        </tr>

        <tr class="heading">
            <td colspan="2">Деловые линии</td>
        </tr>
        <tr>
            <td width="50%">Активен</td>
            <td width="50%"><input type="checkbox" name="DL_ACTIVE" value="Y"<?if($arFields["DL_ACTIVE"] == "Y") echo " checked"?>></td>
        </tr>
        <tr>
            <td width="50%">ApiKey</td>
            <td width="50%">
                <input type="text" name="DL_KEY" value="<?=$arFields["DL_KEY"]?>" size="30" maxlength="100">
                <a href="https://dev.dellin.ru/registration/" target="_blank">Получить ApiKey Деловые линии</a>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2">ЖелДорЭкспедиция</td>
        </tr>
        <tr>
            <td width="50%">Активен</td>
            <td width="50%"><input type="checkbox" name="JDE_ACTIVE" value="Y"<?if($arFields["JDE_ACTIVE"] == "Y") echo " checked"?>></td>
        </tr>

        <tr class="heading">
            <td colspan="2">Энергия</td>
        </tr>
        <tr>
            <td>Активен</td>
            <td><input type="checkbox" name="ENERGY_ACTIVE" value="Y"<?if($arFields["ENERGY_ACTIVE"] == "Y") echo " checked"?>></td>
        </tr>
        <tr>
            <td width="50%">ApiKey</td>
            <td width="50%">
                <input type="text" name="ENERGY_API_KEY" value="<?=$arFields["ENERGY_API_KEY"]?>" size="30" maxlength="100">
                <a href="http://apidoc.nrg-tk.ru/v3/ru/" target="_blank">Получить ApiKey Энергия</a>
            </td>
        </tr>

        <?$tabControl->BeginNextTab();?>
        <tr class="heading">
            <td colspan="2">Кэширование</td>
        </tr>
        <tr>
            <td width="30%"></td>
            <td width="70%">
                <input data-action="clearPoints" type="button" value="Удалить список терминалов ТК" class="clearCacheJs">
                <input data-action="clearPrices" type="button" value="Очистить кэш стоиомсти доставки" class="clearCacheJs">
            </td>
        </tr>

        <?$tabControl->BeginNextTab();?>
        <tr class="heading">
            <td colspan="2">Загрузка пунктов ТК</td>
        </tr>
        <tr>
            <td width="30%"></td>
            <td width="70%">
                <div>
                    <input data-action="getPecPoints" type="button" value="ПЭК" style="width: 250px" class="getPointsJs">
                </div>
                <br>
                <div>
                    <input data-action="getSdekPoints" type="button" value="Сдек" style="width: 250px" class="getPointsJs">
                </div>
                <br>
                <div>
                    <input data-action="getJdePoints" type="button" value="ЖелДорЭкспедиция" style="width: 250px" class="getPointsJs">
                </div>
                <br>
                <div>
                    <input data-action="getDellinPoints" type="button" value="Деловые линии" style="width: 250px" class="getPointsJs">
                </div>
                <br>
                <div>
                    <input data-action="getEnergyPoints" type="button" value="Энергия" style="width: 250px" class="getPointsJs">
                </div>
                <br>

            </td>
        </tr>
        <tr>
            <td width="100%" colspan="2">
                <p>Лог</p>
                <div class="logJs" style="background-color: #fff;padding: 5px 10px;border: 1px solid #e0e8ea;height: 200px;overflow-y: auto;"></div>
            </td>
        </tr>
        <?$tabControl->Buttons();?>
        <input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>">
        <?$tabControl->End();?>
    </form>
<?endif;?>

<script>

$( document ).ready(function() {
    DevelopXTk_ = new DevelopXTk();
});

</script>