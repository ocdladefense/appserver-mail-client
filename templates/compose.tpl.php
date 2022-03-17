
<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/compose.css" />
<script type="application/javascript" src="/modules/mail/assets/js/compose.js">
</script>



<h3>Compose Email(s)</h3>


<div class="panel panel-left">

    <form action="/mail/send" method="post">

        <div class="form-group">
            <label>Template:</label>
            <?php print Html\Select("template", $templates,"default"); ?>
        </div>


        <div class="form-group">
            <label>To:</label>
            <input required id="to" type="email" class="form-control" name="to" value="<?php print $defaultFrom; ?>" aria-describedby="emailHelp" placeholder="To..." />
        </div>

        <div class="form-group">
            <label>From:</label>
            <input required type="email" class="form-control" name="from" aria-describedby="emailHelp" placeholder="From..." />
        </div>
        
        <div class="form-group">
            <label>Email Subject</label>
            <input type="text" class="form-control" name="subject" value="" placeholder="Enter email subject line..." />
        </div>

        <div id="custom-fields">

        </div>


        <button type="submit" class="btn btn-primary">Send Mail</button>
    </form>

</div>

<div class="panel panel-right">
    <div id="preview" class="">

    </div>


    <div id="body-container" class="form-group">
        <label>Email Body</label>
        <textarea id="email-body" class="form-control email-body" name="body" value="" placeholder="Type message here...">&nbsp;</textarea>
    </div>

</div>