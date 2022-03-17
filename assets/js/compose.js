/**
 * 
 * Return HTML reflecting additions to the base email composition form.
 */




const mailer = (function() {
    

    let composer,

    template,

    emailtype,

    currentEmailType,

    CUSTOM_FIELDS_URL = "/mail/compose/custom-fields",

    PREVIEW_URL = "/mail/compose/preview";



    function init() {
        composer = document.getElementById("composer");
        emailtype = document.getElementById("emailtype");
        composer.addEventListener("change", update);
        currentEmailType = emailtype.value;
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



    function update(e) {

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

