
let vehicleList = [];

function getVehicleAll() {
  const formData = new FormData();
  formData.append("type", "view");
  formData.append("vehicle", "all");

  $.ajax({
    url: `controller/getvehicle.php`,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (res) {
      if (res.success && res.data) {
        vehicleList = res.data;
        console.log("Vehicle Data Loaded:", vehicleList);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
    },
  });
}

getVehicleAll();


// Debounce function
function debounce(func, delay) {
  let timer;
  return function (...args) {
    clearTimeout(timer);
    timer = setTimeout(() => func.apply(this, args), delay);
  };
}

function initVehicleSearch(inputSelector, formSelector, handleVehicleSearch, type = "") {
  let $input = $(inputSelector);
  let $form = $(formSelector);
  let $autocomplete = $(inputSelector).siblings(`.autocomplete-suggestions${type}`);
  let isSubmitting = false; // Add a flag to track if a submit is in progress.

  function validateVehicle(inputVal) {
    return vehicleList.some(vehicle => vehicle.Reg.toLowerCase() === inputVal.toLowerCase());
  }

  function handleValidation(e) {
    // if (isSubmitting) return; // Prevent submitting multiple times.

    let inputVal = $input.val().trim();
    let isValid = validateVehicle(inputVal);

    if (!isValid) {
      showNotification("error", "Vehicle not found. Please select a valid vehicle.");
      return false;
    } else {
      isSubmitting = true; // Set flag to true when submitting
      if (type) {
        handleVehicleSearch(e, type); // Call the function dynamically
      } else {
        handleVehicleSearch(e); // Call the function dynamically
      }
    }
  }

  // Listen for Enter key inside the input field
  document.querySelector(inputSelector).addEventListener("keydown", function (e) {
    if (e.key === "Enter") { // Prevent multiple submissions
      e.preventDefault();
      handleValidation(e);
    }
  });

  // Prevent form submission if input is not in the vehicle list
  $form.on("submit", function (e) {
    e.preventDefault();
    if (!isSubmitting) { // Prevent submitting multiple times
      handleValidation(e);
    }
  });

  // Function to show suggestions
  function showSuggestions(input) {
    let searchVal = input.toLowerCase();
    let suggestions = vehicleList.filter(vehicle =>
      vehicle.Reg.toLowerCase().includes(searchVal)
    );

    let suggestionHTML = suggestions.length > 0
      ? suggestions.map(v => `<div class="autocomplete-suggestion autocomplete-suggestion${type}" data-value="${v.Reg}">${v.Reg}</div>`).join("")
      : `<div class="no-results">No results found</div>`;

    $autocomplete.html(suggestionHTML).show();
  }

  // Debounced input event listener
  $input.on("input", debounce(function () {
    let inputVal = $(this).val().trim();
    if (inputVal.length > 0) {
      showSuggestions(inputVal);
    } else {
      $autocomplete.hide();
    }
  }, 300));

  // Handle clicking on a suggestion
  $(document).on("click", `.autocomplete-suggestion${type}`, function (e) {
    $input.val($(this).data("value"));
    $autocomplete.hide();
    handleValidation(e);
  });

  // Hide suggestions when clicking outside
  $(document).on("click", function (e) {
    if (!$(e.target).closest($form).length) {
      $autocomplete.hide();
    }
  });
}
