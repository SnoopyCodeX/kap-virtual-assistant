<div class="container">
    <div class="modal modal-md fade" id="chatuiModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="background: rgba(0,0,0,0); box-shadow: none; border: none;">
                <div class="modal-body" style="background: rgba(0,0,0,0); padding:0;">
                    <div class="chatbox__support">
                        <div class="chatbox__header">
                            <div class="chatbox__image--header">
                                <img src="../../assets/images/woman.png" alt="Profile Photo" width="50px" height="50px">
                            </div>
                            <div class="chatbox__content--header">
                                <h4 class="chatbox__heading--header">Virtual Agent</h4>
                                <p class="chatbox__description--header">
                                    ASK.Kap's Virtual Chat Support, you can ask me anything!
                                </p>
                            </div>
                        </div>

                        <div class="chatbox__messages">
                            <div id="chatbox__messages--container">
                                <div class="messages__item messages__item--visitor">
                                    Hi, I am ASK.KAP's virtual chat assistant, what can I do for you?
                                </div>
                            </div>
                        </div>

                        <form id="chatbox__form" onsubmit="return false;">
                            <div class="chatbox__footer">
                                <input type="text" name="message" id="message" placeholder="Write your message...">
                                <button class="chatbox__send--footer btn" disabled><i class="fa fa-paper-plane"></i> Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>