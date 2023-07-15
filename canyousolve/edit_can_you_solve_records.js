'use strict';
// edit_can_you_solve_records.js
(function () {
    document.addEventListener("DOMContentLoaded", function () {
        const searchForm = document.getElementById("searchForm");
        const editForm = document.getElementById("data_entry_form");
        const idInput = document.getElementById("id");
        const image_for_edit_record = document.getElementById("image_for_edited_record");
        const category = document.getElementById("category");
        const question = document.querySelector('.heading');
        const answer = document.getElementById("content");
        const resultInput = document.getElementById("searchTerm");

        const select_id = document.querySelector('select[name="id"]');

        async function displayRecord(searchTerm = null, selected_id = null) {

            const requestData = {};
            if(searchTerm !== null) requestData.searchTerm = searchTerm;
            if(selected_id !== null) requestData.id = selected_id;
            // console.log('searchTerm', searchTerm);
            try {
                const response = await fetch("search_records.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(requestData),
                });

                const data = await response.json();
                // console.log(data); // Add this line
                if (data.message) {
                    resultInput.value = '';
                    resultInput.placeholder = data.message;
                } else if (data.error) {
                    console.error(data.error);
                } else {
                    const row = data[0];
                    //console.log(row.category);
                    idInput.value = row.id;
                    image_for_edit_record.src = "../" + row.canvas_images;
                    image_for_edit_record.alt = row.answer;
                    category.value = row.category;
                    category.textContent = `${row.category.charAt(0).toUpperCase()}${row.category.slice(1)}`;
                    question.value = row.question;
                    answer.value = row.answer;
                }
            } catch (error) {
                console.error("Error:", error);
            }
        }

        searchForm.addEventListener("submit", function (event) {
            // Prevent the default form submit behavior
            event.preventDefault();

            // Get the value of the search term input field and the select box
            const searchTermInput = document.getElementById("searchTerm").value;
            const selected_id = document.querySelector('select[name="id"]').value;
            // console.log(searchTermInput, selectedHeading);
            // Use the input value if it's not empty, otherwise use the select value
            const searchTerm = searchTermInput !== "" ? searchTermInput : null;
            const search_id = selected_id !== "" ? selected_id : null;
            // Call the displayRecord function with the search term and selected heading
            displayRecord(searchTerm, search_id);
        });


        // New event listener for the dropdown change
        select_id.addEventListener("change", function() {
            const selectedHeading = select_id.options[select_id.selectedIndex].value;
            displayRecord(null, selectedHeading);
        });

        async function fetchUpdatedData() {
            // replace this URL with the URL to fetch updated data
            const response = await fetch("fetch_updated_data.php");

            if (response.ok) {
                const data = await response.json();

                // Get the select element
                const selectBox = document.querySelector('select[name="id"]');

                // Clear current options
                // Clear the dropdown
                selectBox.textContent = '';

                // Create a new option element
                let opt = document.createElement('option');
                opt.value = "";
                opt.disabled = true;
                opt.selected = true;
                opt.textContent = 'Select Heading';

                // Append the option to the select dropdown
                selectBox.appendChild(opt);


                // Populate the select element with new options
                data.forEach(record => {
                    const option = document.createElement('option');
                    option.value = record.id;
                    option.text = record.question;
                    selectBox.appendChild(option);
                });
            } else {
                console.error("Error fetching data:", response.status, response.statusText);
            }
        }
        // Add an event listener to the edit form's submit event
        editForm.addEventListener("submit", async function (event) {
            // Prevent the default form submit behavior
            event.preventDefault();

            // Create a FormData object from the edit form
            const formData = new FormData(editForm);
             // for (var pair of formData.entries()) {
             //   console.log(pair[0]+ ', '+ pair[1]);
             // }

            // Send a POST request to the edit_update_blog.php endpoint with the form data
            const response = await fetch("update_record.php", {
                method: "POST",
                body: formData,
            });

            // Check if the request was successful
            if (response.ok) {
                const result = await response.json();
                // console.log('result', result);
                // If the response has a "success" property and its value is true, clear the form
                if (result.success) {
                    resultInput.value = '';          // Clear the current value of the search input field
                    resultInput.placeholder = "New Search"; // Set the placeholder to `New Search`
                    image_for_edit_record.src = "";
                    image_for_edit_record.alt = "";
                    editForm.reset(); // Resetting the edit form
                    searchForm.reset(); // Resetting the search form

                    // Reset select box to default (first) option
                    const selectBox = document.querySelector('select[name="id"]');
                    selectBox.selectedIndex = 0;
                    fetchUpdatedData();
                }

            } else {
                console.error(
                    "Error submitting the form:",
                    response.status,
                    response.statusText
                );
                // Handle error response
            }
        });


    });
})();