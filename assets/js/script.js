jQuery(document).ready(function ($) {
    console.log("Wisor Events: Script loaded successfully.");

    let button = $("#load-more-events");

    button.on("click", function () {
        let page = parseInt(button.attr("data-page")) || 1;
        let layout = button.attr("data-layout") || "list";
        let eventsPerPage = parseInt(button.attr("data-events-per-page")) || 6;
        let container = $(".wisor-events-container");
        let eventList = container.find(".wisor-events"); // Target the existing <ul> or grid container

        console.log("Load More clicked. Fetching page:", page);
        console.log("Layout:", layout, "Events per page:", eventsPerPage);

        button.text("Loading...").prop("disabled", true);

        $.ajax({
            url: wisor_ajax.ajax_url,
            type: "POST",
            data: {
                action: "wisor_load_more_events",
                security: wisor_ajax.security,
                paged: page + 1,
                layout: layout,
                events_per_page: eventsPerPage,
                event_description_length: button.attr("data-desc-length") // ✅ Pass the description length
            },
            beforeSend: function () {
                console.log("Sending AJAX request...");
            },
            success: function (response) {
                console.log("AJAX response received:", response);

                if (response.success) {
                    let newEvents = $(response.data.html);
                    newEvents.find('.event-content p').each(function () {
                        let descLength = parseInt(button.attr("data-desc-length")) || 10; // Get the description length
                        let fullText = $(this).text().trim();
                        
                        if (fullText.length > descLength) {
                            $(this).text(fullText.substring(0, descLength) + "...");
                        }
                    });
                    if (eventList.length) {
                        eventList.append(newEvents); // Append inside the existing <ul>
                    } else {
                        console.error("Error: Event list <ul> not found.");
                    }

                    button.attr("data-page", page + 1);
                    button.text("Load More").prop("disabled", false);
                    console.log("New events loaded successfully.");
                } else {
                    console.warn("No more events to load.");
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
