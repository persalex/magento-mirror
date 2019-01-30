
potokyAlertAnonymous = {

    createForm: function (templateId, actionUrl, event) {
        event.preventDefault();
        var formId = templateId + '-popup';
        var present = document.getElementById(formId);
        if (present) {
            present.remove();
        }
        var elem = document.getElementById(templateId),
            form = document.createElement('form'),
            fieldset = document.createElement('fieldset'),
            br = document.createElement('br'),
            label = document.createElement('label'),
            input = document.createElement('input'),
            error = document.createElement('p');
        addAttributes(form, {
            "id": formId,
            "action": actionUrl,
            "method": "post"
        });
        addAttributes(label, {
            "for": "email"
        });
        label.innerHTML = 'Please enter Your email';
        addAttributes(input, {
            "type": "text",
            "name": "email",
            "id": formId + "-email",
            "class": "text ui-widget-content ui-corner-all",
            "style": "width: auto"
        });
        addAttributes(error, {
            "id": "alertanonymous-error"
        });
        fieldset.appendChild(label);
        fieldset.appendChild(input);
        form.appendChild(fieldset);
        form.appendChild(br);
        form.appendChild(error);
        elem.appendChild(form);
        this.createPopup([
            form.id,
            input.id,
            error.id
        ]);

        function addAttributes(element, attributes) {
                for(var i in attributes) {
                    element.setAttribute(i, attributes[i]);
                }
        }
    },

    createPopup: function (ids) {
        var form = jQuery( "#" + ids[0] ),
            input = jQuery( "#" + ids[1] ),
            error = jQuery( "#" + ids[2] ),
            emailRegex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,
            nameLengthRange = [6, 80];
        form.dialog({
            autoOpen: true,
            modal: true,
            buttons: [{
                text: "Submit",
                click: function () {
                    validate()
                }
            },
                {
                    text: "Cancel",
                    click: function () {
                        jQuery( this ).dialog( "close" );
                    }
                }
            ]
        });
        function setError( t ) {
            error
                .text( t )
                .addClass( "ui-state-highlight" );
            setTimeout(function() {
                error.removeClass( "ui-state-highlight", 1500 );
            }, 500 );
        }
        function checkLength( o, min, max ) {
            if ( o.val().length > max || o.val().length < min ) {
                o.addClass( "ui-state-error" );
                setError( "Length of an email must be between " +
                    min + " and " + max + "." );
                return false;
            } else {
                return true;
            }
        }
        function checkRegexp( o, regexp, n ) {
            if ( !( regexp.test( o.val() ) ) ) {
                o.addClass( "ui-state-error" );
                setError( n );
                return false;
            } else {
                return true;
            }
        }
        var validate = function() {
            if (!checkLength(input, nameLengthRange[0], nameLengthRange[1] )) {
                return ;
            }
            if(checkRegexp(input, emailRegex, "eg. name@example.com")) {
                document.getElementById(ids[0]).submit()
            }
        }
    }

};
