<?php

if(isset($_POST['addMessageBtn'])) {
    $newQuestion = $conn->real_escape_string($_POST['question']);
    $newResponse = $conn->real_escape_string($_POST['response']);

    $botMessagesAdd = json_decode(FileUtil::readFile("../../includes/chatbot.inc.json", 1024), true);
    $questions = array_keys($botMessagesAdd);

    if (array_search(strtolower($newQuestion), $questions) === false) {
        $botMessagesAdd[strtolower($newQuestion)] = $newResponse;
        FileUtil::writeFile("../../includes/chatbot.inc.json", json_encode($botMessagesAdd, JSON_PRETTY_PRINT));

        $message = "Successfully added a new bot message!";
        $hasSuccess = true;
        $hasError = false;
    } else {
        $message = "The question is already added to the database!";
        $hasSuccess = false;
        $hasError = true;
    }
}

if(isset($_POST['editMessageBtn'])) {
    $newQuestion = $conn->real_escape_string($_POST['question']);
    $newResponse = $conn->real_escape_string($_POST['response']);

    $botMessagesEdit = json_decode(FileUtil::readFile("../../includes/chatbot.inc.json", 1024), true);
    $questions = array_keys($botMessagesEdit);

    $botMessagesEdit[strtolower($newQuestion)] = $newResponse;
    FileUtil::writeFile("../../includes/chatbot.inc.json", json_encode($botMessagesEdit, JSON_PRETTY_PRINT));

    $message = "Successfully updated the bot message!";
    $hasSuccess = true;
    $hasError = false;
}

if(isset($_POST['deleteMessageBtn'])) {
    $question = $conn->real_escape_string($_POST['key']);

    $botMessagesDelete = json_decode(FileUtil::readFile("../../includes/chatbot.inc.json", 1024), true);
    $questions = array_keys($botMessagesDelete);

    if (array_search(strtolower($question), $questions) !== false) {
        $newBotMessagesDelete = [];

        foreach($botMessagesDelete as $key => $val)
            if($key !== $question)
                $newBotMessagesDelete[$key] = $val;

        FileUtil::writeFile("../../includes/chatbot.inc.json", json_encode($newBotMessagesDelete, JSON_PRETTY_PRINT));
        $message = "Successfully deleted bot message!";
        $hasError = false;
        $hasSuccess = true;

    } else {
        $message = "The question you're trying to delete does not exist!";
        $hasError = true;
        $hasSuccess = false;
    }
}

// Fetch all messages
$botMessages = json_decode(FileUtil::readFile("../../includes/chatbot.inc.json", 1024), true);
?>

<!-- Main Content -->
<div class="container-fluid">

    <?php if ($hasError) { ?>
        <div class="col mb-2">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <span class="text-danger" id="message"><?= $message ?></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    <?php } ?>

    <?php if ($hasSuccess) { ?>
        <div class="col mb-2">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <span class="text-success" id="message"><?= $message ?></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    <?php } ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-robot"></i> ChatBot Messages &nbsp;&nbsp;&nbsp;
                <button type="button" class="btn btn-primary px-auto" style="background-color:#223D3C; border-color:#223D3C;" data-toggle="modal" data-target="#addMessageModal">
                    <i class="fa fa-plus-circle"></i>
                    Add new
                </button>
            </h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="bot-messages" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Response</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($botMessages as $key => $val) { ?>
                            <tr>
                                <td><?= ucfirst($key) ?></td>
                                <td><?= $val ?></td>
                                <td>
                                    <a class="btn btn-info px-auto" href="#" onClick="openEditMessageModal({
                                        key: '<?= $conn->real_escape_string($key) ?>',
                                        val: '<?= $conn->real_escape_string($val) ?>',
                                    })">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                    <a class="btn btn-danger px-auto" href="#" onClick="openDeleteMessageModal({
                                        key: '<?= $conn->real_escape_string($key) ?>',
                                    })">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- End Main Content -->


<!-- Add Modal -->
<div class="modal fade" id="addMessageModal" tabindex="-1" role="dialog" aria-labelledby="addMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMessageModalLabel">Add new message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="addMessageForm" method="POST" autocomplete="off">
                <div class="modal-body">

                    <div class="form-group">
                        <label> Question </label>
                        <input type="text" name="question" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label> Response </label>
                        <textarea name="response" class="form-control" spellcheck="false" required></textarea>
                    </div>

                </div>

                <div class="modal-footer row">
                    <div class="col">
                        <div class="form-group">
                            <button type="button" class="btn btn-block btn-danger" data-dismiss="modal" aria-label="Cancel">Cancel</button>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <button style="background-color:#223D3C; border-color:#223D3C;" type="submit" name="addMessageBtn" class="btn btn-block btn-success">Add</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Add Modal -->



<!-- Edit Modal -->
<div class="modal fade" id="editMessageModal" tabindex="-1" role="dialog" aria-labelledby="editMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMessageModalLabel">Edit message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="editMessageForm" method="POST" autocomplete="off">
                <div class="modal-body">

                    <div class="form-group">
                        <label> Question </label>
                        <input type="text" name="question" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label> Response </label>
                        <textarea name="response" class="form-control" spellcheck="false" required></textarea>
                    </div>

                </div>

                <div class="modal-footer row">
                    <div class="col">
                        <div class="form-group">
                            <button type="button" class="btn btn-block btn-danger" data-dismiss="modal" aria-label="Cancel">Cancel</button>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <button style="background-color:#223D3C; border-color:#223D3C;" type="submit" name="editMessageBtn" class="btn btn-block btn-success" disabled>Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Edit Modal -->



<!-- Delete Modal-->
<div class="modal fade" id="deleteMessageModal" tabindex="-1" role="dialog" aria-labelledby="deleteMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="deleteMessageModalLabel">Delete message</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body">Are you sure you really want to delete this bot message?</div>

            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>

                <form method="POST" id="deleteMessageForm">
                    <input type="hidden" name="key" value="">
                    <button type="submit" name="deleteMessageBtn" class="btn btn-success">Delete</button>
                </form>

            </div>
        </div>
    </div>
</div>
<!-- End Delete Modal-->