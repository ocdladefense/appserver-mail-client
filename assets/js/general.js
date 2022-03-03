/**
 * 
 * Return HTML reflecting additions to the base email composition form.
 */


domReady(function() {

    let tnode = document.getElementById("template");
    tnode.addEventListener("change", updateForm);
});



function getCompositionCustomFields(emailType) {

    return fetch("/mail/compose/custom-fields/"+emailType)
    .then(function(resp) {
        return resp.text();
    });
} 

function getPreview(emailType) {

    return fetch("/mail/compose/preview/"+emailType)
    .then(function(resp) {
        return resp.text();
    });
} 



function updateForm(e) {
    let target = e.target;
    let emailType = target.value; // "car";//moduleName || "car";

    let req = getCompositionCustomFields(emailType);

    req.then(function(html) {  
        let cfnode = document.getElementById("custom-fields");
        cfnode.innerHTML = html;
    });

    let req2 = getPreview(emailType);

    req2.then(function(html) {  
        let emailBodyNode = document.getElementById("email-body");
        emailBodyNode.value = html;
    });
}

