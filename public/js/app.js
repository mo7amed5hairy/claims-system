/**
 * Hospital & Department Selection Handler
 */
document.addEventListener('DOMContentLoaded', function() {
    const departmentRadios = document.querySelectorAll('.dept-radio');
    const hospitalIdInput = document.querySelector('input[name="hospital_id"]');
    
    departmentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const hospitalId = this.dataset.hospital;
            
            // Update hospital_id input value
            if (hospitalIdInput) {
                hospitalIdInput.value = hospitalId;
            } else {
                // Create hidden input if not exists
                const form = this.closest('form');
                if (form) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'hospital_id';
                    input.value = hospitalId;
                    form.appendChild(input);
                }
            }
        });
    });
});
