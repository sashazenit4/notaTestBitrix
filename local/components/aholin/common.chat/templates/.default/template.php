<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

// var_dump($arResult);
global $USER;
?>

<br>

<div class="card-wrapper">
    <div class="card-body">
        <div class="card" style="height: 390px; border-radius: 15px; background-color: rgba(0,0,0,0.08);">
            <div class="card-body__sub">
                    <?
                    $arElement = CommonChat::getMessages($arParams["CHATS_IBLOCK_ID"]);

                    foreach($arElement as $key => $value) {
                        ?>
                        <div class="message">
                            <p class="text"><?echo $value['NAME'];?></p>
                            <div class="info">
                                <span class="sender" onclick="setAnswer()">
                                <?
                                    ob_start();

                                    $APPLICATION->IncludeComponent("bitrix:main.user.link", "", array(
                                        "CACHE_TYPE" => "N",
                                        "CACHE_TIME" => "7200",
                                        "ID" => str_replace('user_', '', $value['PROPERTY_SENDER_VALUE']),
                                        "NAME_TEMPLATE" => "#NOBR##LAST_NAME# #NAME##/NOBR#",
                                        "SHOW_LOGIN" => "Y",
                                        "USE_THUMBNAIL_LIST" => "Y"
                                    ));
                    
                                $html = ob_get_contents();
                                ob_get_clean();
                                echo $html;
                                ?></span>
                                    &nbsp
                                <span class="date"><?=$value['DATE_CREATE'];?></span>
                            </div>
                        </div>
                        <?
                    }?>
            </div>
            <div class="card-footer">
                <div class="input-group">
                    <textarea id="message" class="form-control" placeholder="Type your message..." rows="1"></textarea>

                    <button class="input-group-addon btn btn-primary" id="msgBtn" onclick="newMessage(document.getElementById('message').value, <?=$USER->GetID()?>, <?=$arParams['CHATS_IBLOCK_ID']?>)">
                        Send
                    </button>
                    </textarea>
                </div>
            </div>
        </div>
    </div>
</div>