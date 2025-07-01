// function generatePdf(data, sendtoMail = false) {
//     console.log(data);
//     const map1 = window.open('', 'Map', 'status=0,title=0,height=900,width=900,scrollbars=1');
//     const form = document.createElement('form');
//     form.method = 'POST';
//     form.action = data.status == 'completed' ? 'pdf/index.php' : 'pdf/worderorder.php';
//     form.target = 'Map';
//     Object.keys(data).forEach((key) => {
//         const input = document.createElement('input');
//         input.type = 'hidden';
//         input.name = key;
//         input.value = data[key];
//         form.appendChild(input);
//     });
//     document.body.appendChild(form);
//     form.submit();
//     document.body.removeChild(form);
// }
function generatePdf(data, sendToMail = false) {
    const mode = sendToMail ? 'email' : 'view';

    console.log(data.status);

    if (sendToMail) {
        // Send to email
        const form = new FormData();

        // Add mode to form data
        form.append('mode', mode);

        // Append all data to the FormData object
        Object.keys(data).forEach((key) => {
            form.append(key, data[key]);
        });

        // Make the AJAX request
        $.ajax({
            url: 'pdf/index.php', // Update the URL as needed
            type: 'POST',
            data: form,
            processData: false, // Don't process FormData into a query string
            contentType: false, // Let the browser set the content type (multipart/form-data)
            beforeSend: function () {
                handleLoader('flex');
            },
            success: function (response) {
                const res = JSON.parse(response);
                if (res.success) {
                    showNotification('success', res.message || 'Email sent successfully.');
                    var statusModal = bootstrap.Modal.getInstance(document.getElementById('recordExpense'));
                    statusModal?.hide();
                    getRfqData()
                } else {
                    showNotification('error', res.message || 'Failed to send email.');
                }
            },
            error: function () {
                showNotification('error', 'Error sending email.');
            },
            complete: function () {
                handleLoader('none');
            },
        });
    } else {
        // Open in new tab for viewing
        const map1 = window.open('', 'Map', 'status=0,title=0,height=900,width=900,scrollbars=1');
        const form = document.createElement('form');
        form.method = 'POST';

        console.log('data.jobSatisfaction');
        console.log(data.jobSatisfaction);
        form.action = data.jobSatisfaction ? 'pdf/index.php' : 'pdf/worderorder.php'; // Update the URL as needed
        // form.action = data.jobSatisfaction ? 'pdf/index.php' : 'pdf/index.php'; // Update the URL as needed
        form.target = 'Map';

        // Add mode to form data
        const modeInput = document.createElement('input');
        modeInput.type = 'hidden';
        modeInput.name = 'mode';
        modeInput.value = mode;
        form.appendChild(modeInput);

        // Append all data as hidden inputs
        Object.keys(data).forEach((key) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            if (typeof data[key] === 'object') {
                input.value = JSON.stringify(data[key]);
            } else {
                input.value = data[key];
            }
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
}
