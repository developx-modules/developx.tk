<?

use Bitrix\Main\Loader;

global $USER;
if ($USER->IsAdmin()):

    $moduleName = 'developx.tk';
    Loader::includeModule($moduleName);
    Loader::includeModule('sale');
    $moduleObj = Developx\Tk\Options::getInstance();

    $arProps = $moduleObj->arOptions;

    if ($_POST['Update'] && check_bitrix_sessid()) {
        foreach ($arProps as $propCode => $prop) {
            $arProps[$propCode] = $_POST[$propCode];
            COption::SetOptionString($moduleName, $propCode, $_POST[$propCode]);
        }
        LocalRedirect($_SERVER['REQUEST_URI']);
        die();
    }

    $locationTypes = [];
    $res = \Bitrix\Sale\Location\TypeTable::getList(array(
        'select' => array('*', 'NAME_RU' => 'NAME.NAME'),
        'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID)
    ));
    while ($item = $res->fetch()) {
        $locationTypes[] = $item;
    }

    CJSCore::Init('jquery');
    $APPLICATION->AddHeadScript('/bitrix/js/' . $moduleName . '/main.js');

    $aTabs = array(
        array("DIV" => "edit1", "TAB" => "������� ���������", "ICON" => "main_user_edit", "TITLE" => "������� ���������"),
        array("DIV" => "edit2", "TAB" => "��������� ��", "ICON" => "main_user_edit", "TITLE" => "��������� ������������ ��������"),
        array("DIV" => "edit3", "TAB" => "�����������", "ICON" => "main_user_edit", "TITLE" => "�����������"),
        array("DIV" => "edit4", "TAB" => "�������� ������� ��", "ICON" => "main_user_edit", "TITLE" => "�������� ������� ��"),
    );
    $tabControl = new CAdminTabControl("tabControl", $aTabs);

    $tabControl->Begin(); ?>
    <form name="kombox_filter_options" method="POST"
          action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&amp;lang=<? echo LANG ?>&amp;mid_menu=1">
        <?= bitrix_sessid_post(); ?>
        <? $tabControl->BeginNextTab(); ?>
        <tr class="heading">
            <td colspan="2">���������</td>
        </tr>

        <tr>
            <td width="40%">�����-�����������</td>
            <?
            $cityDef = COption::GetOptionString('sale', 'location', false);
            $arLocs = CSaleLocation::GetByID($cityDef, LANGUAGE_ID);
            ?>
            <td width="60%">
                <input disabled type="text" name="" value="<?= $arLocs["CITY_NAME_LANG"] ?>" size="30" maxlength="100">
                <br>
                <div class="pop-text">�����-����������� ������������ � <a
                            href="/bitrix/admin/settings.php?lang=ru&amp;mid=sale" target="_blank">����������
                        ��������-��������</a> � �������� "����� ��������" -&gt; �������������� ��������.
                </div>

            </td>
        </tr>

        <tr>
            <td width="40%">��� �������������� "�����"</td>
            <?

            ?>
            <td width="60%">
                <select name="CITY_TYPE_ID">
                    <? foreach ($locationTypes as $type) {
                        ?>
                        <?
                        $selected = '';
                        if (!empty($arProps['CITY_TYPE_ID'])) {
                            if ($arProps['CITY_TYPE_ID'] == $type['ID']) {
                                $selected = 'selected';
                            }
                        } else {
                            if ($type['CODE'] == 'CITY') {
                                $selected = 'selected';
                            }
                        }
                        ?>
                        <option value="<?= $type['ID'] ?>" <?= $selected ?>><?= $type['NAME_RU'] ?></option>
                    <? } ?>
                </select>
            </td>
        </tr>

        <tr>
            <td width="50%">��������� ���� �������� �� (��.)<a name="opt_termInc"></a>
            </td>
            <td width="50%"><input type="text" size="1" maxlength="255" value="<?= $arProps["DAYS_DOP"] ?>"
                                   name="DAYS_DOP">
            </td>
        </tr>
        <tr>
            <td width="50%">��������� ��������� �������� �� (���.)<a name="opt_termInc"></a>
            </td>
            <td width="50%"><input type="text" size="5" maxlength="255" value="<?= $arProps["PRICE_DOP"] ?>"
                                   name="PRICE_DOP">
            </td>
        </tr>

        <tr class="heading">
            <td colspan="2">����������� �������� ������</td>
        </tr>
        <tr>
            <td width="40%">���, ��</td>
            <td width="60%"><input type="text" name="DEF_WEIGHT" value="<?= $arProps["DEF_WEIGHT"] ?>" size="30"
                                   maxlength="100"></td>
        </tr>
        <tr>
            <td width="40%">�����, �</td>
            <td width="60%"><input type="text" name="DEF_LENGTH" value="<?= $arProps["DEF_LENGTH"] ?>" size="30"
                                   maxlength="100"></td>
        </tr>
        <tr>
            <td width="40%">������, �</td>
            <td width="60%"><input type="text" name="DEF_WIDE" value="<?= $arProps["DEF_WIDE"] ?>" size="30"
                                   maxlength="100"></td>
        </tr>
        <tr>
            <td width="40%">������, �</td>
            <td width="60%"><input type="text" name="DEF_HEIGHT" value="<?= $arProps["DEF_HEIGHT"] ?>" size="30"
                                   maxlength="100"></td>
        </tr>


        <? $tabControl->BeginNextTab(); ?>
        <tr class="heading">
            <td colspan="2">���</td>
        </tr>
        <tr>
            <td width="50%">�������</td>
            <td width="50%"><input type="checkbox" name="PEC_ACTIVE"
                                   value="Y"<? if ($arProps["PEC_ACTIVE"] == "Y") echo " checked" ?>></td>
        </tr>
        <tr>
            <td width="50%">ApiKey</td>
            <td width="50%">
                <input type="text" name="PEC_KEY" value="<?= $arProps["PEC_KEY"] ?>" size="30" maxlength="100">
                <a href="https://pecom.ru/business/developers/api/" target="_blank">�������� ApiKey ���</a>
            </td>
        </tr>

        <tr class="heading">
            <td colspan="2">����</td>
        </tr>
        <tr>
            <td width="50%">�������</td>
            <td width="50%"><input type="checkbox" name="CDEK_ACTIVE"
                                   value="Y"<? if ($arProps["CDEK_ACTIVE"] == "Y") echo " checked" ?>></td>
        </tr>

        <tr class="heading">
            <td colspan="2">������� �����</td>
        </tr>
        <tr>
            <td width="50%">�������</td>
            <td width="50%"><input type="checkbox" name="DL_ACTIVE"
                                   value="Y"<? if ($arProps["DL_ACTIVE"] == "Y") echo " checked" ?>></td>
        </tr>
        <tr>
            <td width="50%">ApiKey</td>
            <td width="50%">
                <input type="text" name="DL_KEY" value="<?= $arProps["DL_KEY"] ?>" size="30" maxlength="100">
                <a href="https://dev.dellin.ru/registration/" target="_blank">�������� ApiKey ������� �����</a>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2">����������������</td>
        </tr>
        <tr>
            <td width="50%">�������</td>
            <td width="50%"><input type="checkbox" name="JDE_ACTIVE"
                                   value="Y"<? if ($arProps["JDE_ACTIVE"] == "Y") echo " checked" ?>></td>
        </tr>

        <tr class="heading">
            <td colspan="2">�������</td>
        </tr>
        <tr>
            <td>�������</td>
            <td><input type="checkbox" name="ENERGY_ACTIVE"
                       value="Y"<? if ($arProps["ENERGY_ACTIVE"] == "Y") echo " checked" ?>></td>
        </tr>
        <tr>
            <td width="50%">ApiKey</td>
            <td width="50%">
                <input type="text" name="ENERGY_API_KEY" value="<?= $arProps["ENERGY_API_KEY"] ?>" size="30"
                       maxlength="100">
                <a href="http://apidoc.nrg-tk.ru/v3/ru/" target="_blank">�������� ApiKey �������</a>
            </td>
        </tr>

        <? $tabControl->BeginNextTab(); ?>
        <tr class="heading">
            <td colspan="2">�����������</td>
        </tr>
        <tr>
            <td width="30%"></td>
            <td width="70%">
                <input data-action="clearPoints" type="button" value="������� ������ ���������� ��"
                       class="clearCacheJs">
                <input data-action="clearPrices" type="button" value="�������� ��� ��������� ��������"
                       class="clearCacheJs">
            </td>
        </tr>

        <? $tabControl->BeginNextTab(); ?>
        <tr class="heading">
            <td colspan="2">�������� ������� ��</td>
        </tr>
        <tr>
            <td width="30%"></td>
            <td width="70%">
                <div>
                    <input data-action="getPecPoints" type="button" value="���" style="width: 250px"
                           class="getPointsJs">
                </div>
                <br>
                <div>
                    <input data-action="getSdekPoints" type="button" value="����" style="width: 250px"
                           class="getPointsJs">
                </div>
                <br>
                <div>
                    <input data-action="getJdePoints" type="button" value="����������������" style="width: 250px"
                           class="getPointsJs">
                </div>
                <br>
                <div>
                    <input data-action="getDellinPoints" type="button" value="������� �����" style="width: 250px"
                           class="getPointsJs">
                </div>
                <br>
                <div>
                    <input data-action="getEnergyPoints" type="button" value="�������" style="width: 250px"
                           class="getPointsJs">
                </div>
                <br>

            </td>
        </tr>
        <tr>
            <td width="100%" colspan="2">
                <p>���</p>
                <div class="logJs"
                     style="background-color: #fff;padding: 5px 10px;border: 1px solid #e0e8ea;height: 200px;overflow-y: auto;"></div>
            </td>
        </tr>
        <? $tabControl->Buttons(); ?>
        <input type="submit" name="Update" value="<?= GetMessage("MAIN_SAVE") ?>">
        <? $tabControl->End(); ?>
    </form>
<? endif; ?>

<script>
    $(document).ready(function () {
        DevelopXTk_ = new DevelopXTk();
    });
</script>