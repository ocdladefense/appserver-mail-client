/**
 * 
 * Return HTML reflecting additions to the base email composition form.
 */




const mailer = (function() {
    

    let composer,

    testButton,

    sendButton,

    template,

    emailtype,

    currentEmailType,

    CUSTOM_FIELDS_URL = "/mail/compose/fields",

    PREVIEW_URL = "/mail/preview",

    TEST_URL = "/mail/test",

    SEND_URL = "/mail/send";



    function init() {
        composer = document.getElementById("composer");
        emailtype = document.getElementById("emailtype");
        composer.addEventListener("change", update);
        // composer.addEventListener("submit", update);
        testButton = document.getElementById("test-mail");
        currentEmailType = emailtype.value;
        testButton.addEventListener("click", testMail);
    }



    function getCustomFields(data) {
        let template = data.get("emailtype");

        return fetch(CUSTOM_FIELDS_URL+"/"+template)
        .then(function(resp) {
            return resp.text();
        });
    } 




    function getPreview(data) {
        var template = data.get("emailtype");
        
        return fetch(PREVIEW_URL+"/"+template,
        {
            method: 'POST',
            body: data
        })
        .then(function(resp) {
            return resp.text();
        });
    }


    function testMail(e) {
        e.preventDefault();
        e.stopPropagation();

        let composer = document.getElementById("composer");
        let data = new FormData(composer);

        var template = data.get("emailtype");
        
        let sent = fetch(TEST_URL+"/"+template,
        {
            method: 'POST',
            body: data
        })
        .then(function(resp) {
            return resp.json();
        });

        sent.then(function(statuses){
            console.log(statuses);
            alert("A test mesage for "+template+" was sent to your account");
        });
    }



    function sendMail(data) {
        var template = data.get("emailtype");
        
        return fetch(PREVIEW_URL+"/"+template,
        {
            method: 'POST',
            body: data
        })
        .then(function(resp) {
            return resp.text();
        });
    }



    function update(e) {
        e.preventDefault();
        e.stopPropagation();

        var form = e.currentTarget;
        var data = new FormData(form);
        let body = document.getElementById("body");
        let pnode = document.getElementById("preview");
        let fnode = document.getElementById("custom-fields");



        if(currentEmailType != data.get("emailtype")) {
            currentEmailType = data.get("emailtype");
            getCustomFields(data).then(function(html) {  
                fnode.innerHTML = html;
            });
        }

        
        getPreview(data).then(function(html) {  
            pnode.innerHTML = html;
        });
    }

    return init;
})();

domReady(mailer);

