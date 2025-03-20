jQuery(document).ready(function ($) {
    console.log("Wisor Events: Script loaded successfully.");

    let button = $("#load-more-events");

    button.on("click", function () {
        let page = parseInt(button.attr("data-page")) || 1;
        let layout = button.attr("data-layout") || "list";
        let eventsPerPage = parseInt(button.attr("data-events-per-page")) || 6;
        let securityToken = wisor_ajax.security;

        console.log("Load More clicked. Fetching page:", page);
        console.log("Layout:", layout, "Events per page:", eventsPerPage);
        console.log("Security Token:", securityToken);

        let requestData = {
            action: "wisor_load_more_events",
            security: securityToken,
            paged: page + 1,
            layout: layout,
            events_per_page: eventsPerPage,
        };

        console.log("Sending AJAX request with data:", requestData);

        button.text("Loading...").prop("disabled", true);

        $.ajax({
            url: wisor_ajax.ajax_url,
            type: "POST",
            data: requestData,
            beforeSend: function () {
                console.log("Sending AJAX request...");
            },
            success: function (response) {
                console.log("AJAX response received:", response);

                if (response.success) {
                    $(".wisor-events-container").append(response.data.html);
                    button.attr("data-page", page + 1);
                    button.text("Load More").prop("disabled", false);
                    console.log("New events loaded successfully.");
                } else {
                    console.warn("No more events to load.");
                    console.error("Error Message:", response.data.message || "Unknown error");
                    button.remove();
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
                console.log("Full Response:", xhr.responseText);
                button.text("Load More").prop("disabled", false);
            }
        });
    });
});
