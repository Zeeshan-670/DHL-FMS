'use strict';
let regUser;
let regVendor;

function getUserList() {
    const formData = new FormData();
    formData.append('type', 'view');

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
            if (data.success) {
                if ($.fn.DataTable.isDataTable('#userdetail')) {
                    $('#userdetail').DataTable().destroy();
                }
                $('#udTbody').empty();
                regUser = data.data;
                regUser = regUser.reverse();


                regUser.forEach(function (el, i) {
                    $('#udTbody').append(`
                        <tr data-id="${el.id}">
                            <td data-field="Sno">${++i}</td>
                            <td data-field="vehicleId" style="display:none">${el.id}</td>
                            <td style="width: 100px">
                                <a class="btn btn-outline-success btn-sm" title="Edit" onclick="handleEdit(this)">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <a class="btn btn-outline-danger btn-sm edit" onclick="handleUserDelete('${el.id}')" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                            <td data-field="username">${el.username || '-'}</td>
                            <td data-field="name">${el.name || '-'}</td>
                            <td data-field="email">${el.email || '-'}</td>
                            <td data-field="password">${decodePassword(el.password)}</td>
                            <td data-field="city">${el.city || '-'}</td>
                            <td data-field="station">${el.station || '-'}</td>
                            <td data-field="designation">${el.designation || '-'}</td>
                            <td data-field="access">${el.access || '-'}</td>
                            <td data-field="signature_url">${el.signature_url || '-'}</td>
                            
                        </tr>
                    `);
                });

                $('#userdetail').DataTable({
                    scrollY: '500px',
                    scrollX: true,
                    scrollCollapse: true,
                });
                adjustTable(`userdetail`)

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
function getVendorList() {
    const formData = new FormData();
    formData.append('type', 'view');

    $.ajax({
        url: 'controller/vendor.php',
        type: 'POST',
        processData: false,
        contentType: false,
        data: formData,
        beforeSend: function () {
            handleLoader('flex');
        },
        success: function (data) {
            if (data.success) {

                console.log(data);
                if ($.fn.DataTable.isDataTable('#vendordetail')) {
                    $('#vendordetail').DataTable().destroy();
                }
                $('#vdTbody').empty();
                regVendor = data.data;
                regVendor = regVendor.reverse();
                regVendor.forEach(function (el, i) {
                    $('#vdTbody').append(`
                        <tr data-id="${el.vendorid}">
                            <td data-field="Sno">${++i}</td>
                            <td data-field="vehicleId" style="display:none">${el.vendorid}</td>
                            <td style="width: 100px">
                                <a class="btn btn-outline-success btn-sm" title="Edit" onclick="handleVendorEdit(this)">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <a class="btn btn-outline-danger btn-sm edit" onclick="handleVendorDelete('${el.vendorid}')" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                            <td data-field="vendorname">${el.vendorname || '-'}</td>
                            <td data-field="username">${el.username || '-'}</td>
                            <td data-field="vendoremail">${el.vendoremail || '-'}</td>
                            <td data-field="password">${decodePassword(el.password)}</td>
                            <td data-field="rating">${el.total_jobs || '-'}</td>
                            <td data-field="rating">${el.avg_rating == '0.00' ? '-' : el.avg_rating + '/10'}</td>
                            <td data-field="vendorcontact">${el.vendorcontact || '-'}</td>
                            <td data-field="city">${el.city || '-'}</td>
                            <td data-field="station">${el.station || '-'}</td>
                            <td data-field="vendoraddress">${el.vendoraddress || '-'}</td>
                            
                        </tr>
                    `);
                });

                $('#vendordetail').DataTable({
                    scrollY: '500px',
                    scrollX: true,
                    scrollCollapse: true,
                });
                adjustTable(`vendordetail`)
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
function getSelectData() {
    const formDataforPopulate = new FormData();
    formDataforPopulate.append('type', 'add');

    $.ajax({
        url: `controller/registration.php`,
        type: 'POST',
        data: formDataforPopulate,
        processData: false,
        contentType: false,
        // beforeSend: function () {
        //     handleLoader('flex');
        // },
        success: function (res) {
            if (res.success) {
                console.log(res);
                populateOptions('city', res.data.cities, 'id', 'name', '-- Select City --');
                populateOptions('designation', res.data.designations, 'id', 'title', '-- Select Designation --');
                populateOptions('station', res.data.stations, 'id', 'name', '-- Select Station --');
                populateDataFromArray('access', res.data.access)
                populateOptions('udcity', res.data.cities, 'id', 'name', '-- Select City --');
                populateOptions('uddesignation', res.data.designations, 'id', 'title', '-- Select Designation --');
                populateOptions('udstation', res.data.stations, 'id', 'name', '-- Select Station --');
                populateOptions('vcity', res.data.cities, 'id', 'name', '-- Select City --');
                populateOptions('vstation', res.data.stations, 'id', 'name', '-- Select Station --');
                populateOptions('vecity', res.data.cities, 'id', 'name', '-- Select City --');
                populateOptions('vestation', res.data.stations, 'id', 'name', '-- Select Station --');
                populateDataFromArray('udaccess', res.data.access)

            }
        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
        },
        // complete: function () {
        //     handleLoader('none');
        // },
    });
}

getUserList();
getVendorList();
getSelectData();


const passwordInput = document.getElementById('password');
passwordInput.addEventListener('input', function () {
    validatePassword(passwordInput);
});



function handleEdit(button) {
    let row = $(button).closest('tr');
    let userId = row.data('id');
    let username = row.find('td[data-field="username"]').text();
    let email = row.find('td[data-field="email"]').text();
    let name = row.find('td[data-field="name"]').text();
    let password = row.find('td[data-field="password"]').text();
    let city = row.find('td[data-field="city"]').text();
    let station = row.find('td[data-field="station"]').text();
    let designation = row.find('td[data-field="designation"]').text();
    let access = row.find('td[data-field="access"]').text();
    let signatureUrl = row.find('td[data-field="signature_url"]').text();

    // Populate modal fields
    $('#udusername').val(username);
    $('#udemail').val(email);
    $('#udId').val(userId);
    $('#udname').val(name);
    $('#udpassword').val(password);

    // Set dropdowns for city, station, designation, and access
    $('#udcity option')
        .filter(function () {
            return $(this).text() === city;
        })
        .prop('selected', true)
        .change();
    $('#udstation option')
        .filter(function () {
            return $(this).text() === station;
        })
        .prop('selected', true)
        .change();
    $('#uddesignation option')
        .filter(function () {
            return $(this).text() === designation;
        })
        .prop('selected', true)
        .change();
    $('#udaccess option')
        .filter(function () {
            return $(this).text() === access;
        })
        .prop('selected', true)
        .change();
    // $('#udstation').val(station).change();
    // $('#uddesignation').val(designation).change();
    // $('#udaccess').val(access).change();
    console.log(signatureUrl || '-');

    if (signatureUrl != 'null') {
        $('#udsignaturePreview').attr('src', window.location.origin + '/FMS/signatures/' + signatureUrl);
    } else {
        $('#udsignaturePreview').attr('src', 'https://cdn-icons-png.flaticon.com/512/992/992651.png');
    }

    // Open the modal
    $('#updateUser').modal('show');
}
function handleVendorEdit(button) {
    let row = $(button).closest('tr');
    let vendorId = row.data('id');
    let vendorname = row.find('td[data-field="vendorname"]').text();
    let username = row.find('td[data-field="username"]').text();
    let password = row.find('td[data-field="password"]').text();
    let vendorcontact = row.find('td[data-field="vendorcontact"]').text();
    let vendoremail = row.find('td[data-field="vendoremail"]').text();
    let city = row.find('td[data-field="city"]').text();
    let station = row.find('td[data-field="station"]').text();
    let vendoraddress = row.find('td[data-field="vendoraddress"]').text();

    // Populate modal fields
    $('#vevendorId').val(vendorId);
    $('#vevendorname').val(vendorname);
    $('#vevendorusername').val(username);
    $('#vevendorpassword').val(password);
    $('#vevendorcontact').val(vendorcontact);
    $('#vevendoremail').val(vendoremail);
    $('#vevendorAddress').val(vendoraddress);

    // Set dropdown for city and station based on text value
    $('#vecity option')
        .filter(function () {
            return $(this).text() === city;
        })
        .prop('selected', true)
        .change();

    $('#vestation option')
        .filter(function () {
            return $(this).text() === station;
        })
        .prop('selected', true)
        .change();

    // Open the modal for updating vendor details
    $('#updateVendor').modal('show');
}

function handleDeleteConfirm(uid, type, apiUrl, successMessage) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append(type, uid);
            formData.append('type', 'delete');

            $.ajax({
                url: `controller/${apiUrl}.php`,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    handleLoader('flex');
                },
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: successMessage,
                            icon: 'success',
                        });
                        if (apiUrl == 'registration') {
                            getUserList();
                        } else if (apiUrl == 'vendor') {
                            getVendorList();
                        }
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong, unable to delete.',
                            icon: 'error',
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.log('Error during form submission:', xhr.responseText);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while processing your request.',
                        icon: 'error',
                    });
                },
                complete: function () {
                    handleLoader('none');
                },
            });
        }
    });
}

function handleUserDelete(uid) {
    handleDeleteConfirm(uid, 'uid', 'registration', 'This User has been deleted.');
}

function handleVendorDelete(vid) {
    handleDeleteConfirm(vid, 'vid', 'vendor', 'This Vendor has been deleted.');
}


// Function to preview signature image when a new file is selected
function previewSignature() {
    const file = document.getElementById('udsignature').files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#udsignaturePreview').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
    }
}

$('#udsignaturePreview').on('click', function () {
    $('#udsignature').click();
});


// Create A form Data 
function createUserFormData() {
    const form = new FormData();
    form.append("type", "insert");
    form.append("username", getDataById('username'));
    form.append("name", getDataById('name'));
    form.append("email", getDataById('email'));
    form.append("password", encodePassword(getDataById('password')));
    form.append("access", getSelectedValue('access'));
    form.append("city_id", getSelectedValue('city'));
    form.append("station_id", getSelectedValue('station'));
    form.append("designation_id", getSelectedValue('designation'));
    const signatureFile = document.getElementById('signature').files[0];
    if (signatureFile) form.append("signature_url", signatureFile);
    console.log(form);
    return form;

}

function updateUserFormData() {
    const form = new FormData();
    form.append("type", "update");
    form.append("username", getDataById('udusername'));
    form.append("name", getDataById('udname'));
    form.append("email", getDataById('udemail'));
    form.append("password", encodePassword(getDataById('udpassword')));
    form.append("access", getSelectedValue('udaccess'));
    form.append("city_id", getSelectedValue('udcity'));
    form.append("station_id", getSelectedValue('udstation'));
    form.append("designation_id", getSelectedValue('uddesignation'));
    form.append("uid", getDataById('udId'));
    const signatureFile = document.getElementById('udsignature').files[0];
    if (signatureFile) { form.append("signature_url", signatureFile); }
    return form;
}

function createVendorFormData() {
    const form = new FormData();
    form.append("type", "insert");
    form.append("vendorname", getDataById('vendorname'));
    form.append("vendorcontact", getDataById('vendorcontact'));
    form.append("vendoraddress", getDataById('vendoraddress'));
    form.append("cityId", getSelectedValue('vcity'));
    form.append("stationId", getSelectedValue('vstation'));
    form.append("username", getDataById('vendorusername'));
    form.append("vendoremail", getDataById('vendoremail'));
    form.append("password", encodePassword(getDataById('vendorpassword')));
    return form;

}
function updateVendorFormData() {
    const form = new FormData();
    form.append("type", "update");
    form.append("vendorname", getDataById('vevendorname'));
    form.append("vendorcontact", getDataById('vevendorcontact'));
    form.append("vendoraddress", getDataById('vevendoraddress'));
    form.append("cityId", getSelectedValue('vecity'));
    form.append("stationId", getSelectedValue('vestation'));
    form.append("username", getDataById('vevendorusername'));
    form.append("vendoremail", getDataById('vevendoremail'));
    form.append("password", encodePassword(getDataById('vevendorpassword')));
    form.append("vid", getDataById('vevendorId'));
    return form;

}


function handleFormSubmission(url, formDataFunction, formId, modalId) {
    document.getElementById(formId).addEventListener('submit', function (e) {
        e.preventDefault();
        let form = formDataFunction();
        $.ajax({
            url: `controller/${url}.php`,
            type: 'POST',
            data: form,
            contentType: false,
            processData: false,
            beforeSend: function () {
                handleLoader('flex');
            },
            success: function (res) {
                if (res.success) {

                    if (url == 'registration') {
                        getUserList();
                    } else if (url == 'vendor') {
                        getVendorList()
                    }
                    showNotification('success', res.message);
                    clearForm(formId);
                    setTimeout(() => {
                        $(`#${modalId}`)?.modal('hide');
                    }, 1000);
                    // }
                } else {
                    showNotification('error', res.message);

                }
            },
            error: function (xhr, status, error) {
                console.log('Error during form submission:', xhr.responseText);
                alert('An error occurred while updating vehicle details.');
            },
            complete: function () {
                handleLoader('none');
            },
        });
    });
}

handleFormSubmission('registration', createUserFormData, 'userForm', 'createUser');
handleFormSubmission('registration', updateUserFormData, 'userUpdateForm', 'updateUser');
handleFormSubmission('vendor', createVendorFormData, 'vendorForm', 'createVendor');
handleFormSubmission('vendor', updateVendorFormData, 'vendorUpdateForm', 'updateVendor');



