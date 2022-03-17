
<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/compose.css" />
<script type="application/javascript" src="/modules/mail/assets/js/compose.js">
</script>






<div class="panel panel-left">
    <h3>Compose mail</h3>


    <form action="/mail/send" id="composer" method="post">
      
        <div class="form-item">
            <label>Template:</label>
            <?php print Html\Select("template", array("default"=>"OCDLA Letterhead"),"default"); ?>
        </div>

        <div class="form-item">
            <label>Email Type:</label>
            <?php print Html\Select("emailtype", $templates,"default"); ?>
        </div>

        <div class="form-item">
            <label>From:</label>
            <input required type="email" name="from" aria-describedby="emailHelp" placeholder="From..." />
        </div>

        <div class="form-item">
            <label>To:</label>
            <input required id="to" type="email" name="to" value="<?php print $defaultFrom; ?>" aria-describedby="emailHelp" placeholder="To..." />
        </div>


        
        <div class="form-item">
            <label>Email Subject</label>
            <input type="text" name="subject" value="" placeholder="Enter email subject line..." />
        </div>

        <div id="custom-fields">

        </div>


        <button type="submit" disabled class="btn btn-primary">Send Mail</button>
    </form>

</div>

<div class="panel panel-right">
    <h3>Preview</h3>

    <div id="preview" class="">

    </div>


    <div id="body-container" class="form-group">
        <label>Email Body</label>
        <textarea id="email-body" class="form-control email-body" name="body" value="" placeholder="Type message here...">&nbsp;</textarea>
    </div>

</div>