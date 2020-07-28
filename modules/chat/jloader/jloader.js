var JLoader = {
    request: function (url, options) {

        if (!options) {
            var options = {};
        }

        var method = options.data ? 'POST' : 'GET';

        if (window.XMLHttpRequest) {

            request = new XMLHttpRequest();
            request.open(method, url, true);
            request.send(options.data);

            request.onreadystatechange = function () {

                if (options.statusToId) {
                    if (document.getElementById(options.statusToId)) {
                        document.getElementById(options.statusToId).innerHTML = getStateText(request.readyState);
                    }
                }

                if (request.readyState == 4) {

                    if (request.status == 200) {
                        
                        if (options.responseToId) {
                            if (document.getElementById(options.responseToId)) {
                                document.getElementById(options.responseToId).innerHTML = request.responseText;
                            }
                        }

                        if (options.responseToIdAdd) {
                            if (document.getElementById(options.responseToIdAdd)) {
                                document.getElementById(options.responseToIdAdd).innerHTML += request.responseText;
                            }
                        }

                        if (options.success) {
                            options.success(request.responseText);
                        }

                        if (options.complete) {
                            options.complete();
                        }

                    } else {
                        if (options.error) {
                            options.error('Response status code is not 200');
                        }
                    }
                }
            }
        } else {
            if (options.error) {
                options.error('XMLHttpRequest is not supported');
            }
        }
    }

}

function getStateText(code) {
    var stateText = 'Error';
    switch (code) {
        case 0: stateText = "Uninitialized"; break;
        case 1: stateText = "Loading"; break;
        case 2: stateText = "Loaded"; break;
        case 3: stateText = "Interactive"; break;
        case 4: stateText = "Complete"; break;
    }
    return stateText;
}