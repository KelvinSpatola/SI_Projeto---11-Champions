function goBack() {
    window.history.back();
}

function redirectPage(_page, _name, _id) {
    var page = _page;
    var name = _name;
    var id = _id;
    var result = page + "?" + name + "=" + id;
    window.location = result;
}