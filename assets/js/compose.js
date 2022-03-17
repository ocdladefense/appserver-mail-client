/**
 * 
 * Return HTML reflecting additions to the base email composition form.
 */


domReady(function() {

    let tnode = document.getElementById("template");
    tnode.addEventListener("change", updateForm);
});



function getCustomFields(template) {

    return fetch("/mail/compose/custom-fields/"+template)
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
    let template = target.value;
    let body = document.getElementById("body");
    let pnode = document.getElementById("preview");
    let fnode = document.getElementById("custom-fields");
    let fhtml = getCustomFields(template);
    let phtml = getPreview(template);


    fhtml.then(function(html) {  
        fnode.innerHTML = html;
    });

    phtml.then(function(html) {  
        pnode.innerHTML = html;
    });
}

