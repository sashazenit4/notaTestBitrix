<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

// var_dump($arResult);
global $USER;
?>
<h1>General chat</h1>

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
                                <span class="sender"><?=$value['PROPERTY_SENDER_VALUE'];?></span>
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