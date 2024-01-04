$.validator.addMethod('validPassword',
            function(value, element, param) {

                if (value != '') {
                    if (value.match(/.*[a-z]+.*/i) == null) {
                        return false;
                    }
                    if (value.match(/.*\d+.*/) == null) {
                        return false;
                    }
                }

                return true;
            },
            'Hasło musi zawierać conajmniej jedną literę oraz jedną cyfrę.'
        );
