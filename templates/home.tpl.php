<?php
    use function Html\createSelectElement;
?>

<br />

<form id="mail-form" action="/mail/show/form" method="post">

    <label><h5>Select Email Type</h5></label>
    <?php print createSelectElement("option", $options, null); ?>

</form>

<br />





<script>

    var select = document.getElementsByTagName("select")[0];

    select.addEventListener("change", function(){
            $form = document.getElementById("mail-form");
            $form.submit();
        });
    
</script>