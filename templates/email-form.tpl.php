
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />
<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/email-form.css" />
<style type="text/css">
    .form-control {
        margin-bottom: 15px;
    }
</style>

<script type="application/javascript" src="/modules/mail/assets/js/general.js">
</script>


<div class="container email-container">

    <form action="/mail/send" method="post">

        <div class="row">
            <div class="col-md-4">
                <h1>Compose Email(s)</h1>


                <div class="form-group">
                    <label>Template:</label>
                    <select id="template" class="form-control" name="template">
                        <option value="standard">Standard Email</option>
                        <option value="car-notification">CAR Notification</option>
                        <option value="bon-expiration-1">Books Online Expiration - First Notice</option>
                        <option value="bon-expiration-2">Books Online Expiration - Second Notice</option>
                    </select>
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

                <div id="body-container" class="form-group">
                    <label>Email Body</label>
                    <textarea id="email-body" class="form-control" name="body" value="" placeholder="Type message here...">&nbsp;</textarea>
                </div>

                <div id="custom-fields">

                </div>

            </div>

            <div class="col-md-6">

                <div id="preview-container" class="form-group scroll email-body preview hidden">
                </div>

            </div>

        </div>





        <button type="submit" class="btn btn-primary">Send Mail</button>

    </form>

</div>