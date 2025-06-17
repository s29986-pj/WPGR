document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('form.form-delete');

    deleteForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            Swal.fire({
                title: 'Czy na pewno?',
                text: "Tej operacji nie będzie można cofnąć!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#999',
                confirmButtonText: 'Tak, usuń to!',
                cancelButtonText: 'Anuluj'
            }).then((result) => {

                if (result.isConfirmed) {

                    form.submit();
                }
            });
        });
    });
});
