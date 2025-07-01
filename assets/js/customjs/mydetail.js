let curPassword;

function getUserDetail() {
    const formData = new FormData();
    formData.append('type', 'getUser');

    $.ajax({
        url: 'controller/registration.php',
        type: 'POST',
        processData: false,
        contentType: false,
        data: formData,
        beforeSend: function () {
            handleLoader('flex');
        },
        success: function (data) {
            console.log(data);

            if (data.success) {
                setDataById('name', data.data[0]?.name)
                setDataById('username', data.data[0]?.username)
                setDataById('city', data.data[0]?.city)
                setDataById('station', data.data[0]?.station)
                setDataById('designation', data.data[0]?.designation)
                setDataById('access', data.data[0]?.access)
                console.log(data.data[0]);

                curPassword = data.data[0]?.password
            }
        },
        error: function (xhr, status, error) {
            console.error('Error in ajax request:', error);
        },
        complete: function () {
            handleLoader('none');
        },
    });
}
getUserDetail();

const passwordInput = document.getElementById('newPassword');
passwordInput.addEventListener('input', function () {
    validatePassword(passwordInput);
});

document.getElementById('updatePassword').addEventListener('submit', function (e) {
    e.preventDefault();
    const oldPassword = encodePassword(document.getElementById('curPassword').value);
    const newPassword = encodePassword(document.getElementById('newPassword').value);

    if (oldPassword !== curPassword) {
        showNotification('error', 'Current password is incorrect');
    } else {
        const formData = new FormData();
        formData.append('type', 'userUpdate');
        formData.append('oldpass', oldPassword);
        formData.append('newpass', newPassword);

        $.ajax({
            url: 'controller/userUpdate.php',
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            beforeSend: function () {
                handleLoader('flex');
            },
            success: function (data) {
                if (data.success) {
                    showNotification('success', 'Password updated successfully');

                    // Redirect to login page after a short delay to show notification
                    setTimeout(function () {
                        window.location.href = 'index.php';
                    }, 2000); // 2 seconds delay for notification to show
                } else {
                    showNotification('error', data.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error in AJAX request:', error);
            },
            complete: function () {
                handleLoader('none');
            },
        });
    }
});
