$(document).ready(function () {
    const apiToken = (typeof token !== "undefined" && token) ? token : localStorage.getItem("token");
    const treatmentId = $(".page-wrapper").data("treatment-id");

    console.log("Extracted Treatment ID from page:", treatmentId);

    if (!treatmentId || isNaN(treatmentId)) {
        alert("Error: Treatment ID is missing or invalid.");
        return;
    }

    if (!apiToken) {
        alert("Authentication token is missing. Please log in again.");
        return;
    }

    $.ajax({
        url: "/api/treatments/" + treatmentId,
        type: "GET",
        dataType: "json",
        headers: {
            "Authorization": "Bearer " + apiToken
        },
        success: function (response) {
            const treatment = response?.treatment ?? response;

            console.log("Fetched Treatment Data:", treatment);

            function ucfirst(str) {
                str = (str !== null && str !== undefined) ? String(str) : "";
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            function formatDate(value) {
                if (!value) return "N/A";

                const normalized = String(value).replace(" ", "T");
                const date = new Date(normalized);

                if (!isNaN(date.getTime())) {
                    return date.toISOString().split("T")[0];
                }

                return String(value).split("T")[0].split(" ")[0];
            }

            function normalizeGstValue(value) {
                if (value === null || value === undefined || value === "") return [];
                if (Array.isArray(value)) return value;

                if (typeof value === "string") {
                    try {
                        const parsed = JSON.parse(value);
                        if (Array.isArray(parsed)) return parsed;
                    } catch (e) {
                        // Fall back to comma-separated parsing below.
                    }

                    return value.split(",").map(v => v.trim()).filter(Boolean);
                }

                return [value];
            }

            function formatGstDisplay(items, priceValue) {
                if (!items || items.length === 0) return "";

                const priceNum = parseFloat(priceValue);

                return items.map(function (item) {
                    if (typeof item === "object" && item !== null) {
                        return `${item.tax_name} (${parseFloat(item.tax_rate).toFixed(2)}%) = ${parseFloat(item.tax_amount).toFixed(2)}`;
                    }

                    const text = String(item);
                    const match = text.match(/(\d+(\.\d+)?)%/);

                    if (match && !isNaN(priceNum)) {
                        const rate = parseFloat(match[1]);
                        const amount = (priceNum * rate) / 100;
                        return `${text} = ${amount.toFixed(2)}`;
                    }

                    return text;
                }).join(", ");
            }

            const price = parseFloat(treatment.price);
            const gstOption = treatment.gst_option ?? "Without GST";

            $(".treatment_name").text(ucfirst(treatment.name || "N/A"));
            $("#doctor_name").text(ucfirst(treatment.doctor_name || "N/A"));
            $("#price").text(!isNaN(price) ? `Rs. ${price.toFixed(2)}` : "N/A");
            $("#description").text(ucfirst(treatment.description || "N/A"));
            $("#treatment_created_at").text(formatDate(treatment.created_at));
            $("#gst_option").text(gstOption);

            if (String(gstOption).toLowerCase() === "with gst") {
                const gstData = normalizeGstValue(treatment.product_gst);

                if (gstData.length > 0) {
                    $("#product_gst").text(formatGstDisplay(gstData, treatment.price));
                    $("#product_gst_div").show();
                } else {
                    $("#product_gst_div").hide();
                }
            } else {
                $("#product_gst_div").hide();
            }

            $(".edit-treatment-btn").attr("href", "/treatment/edit/" + treatment.id);
        },
        error: function (xhr) {
            console.error("Failed to fetch treatment details:", xhr.status, xhr.responseText);

            if (xhr.status === 401) {
                alert("Your session has expired. Please log in again.");
                return;
            }

            alert("Failed to fetch treatment details.");
        }
    });
});

$(document).on("click", ".delete-treatment", function () {
    const apiToken = (typeof token !== "undefined" && token) ? token : localStorage.getItem("token");
    const treatmentId = $(this).data("id");

    if (!apiToken) {
        Swal.fire("Error", "Authentication token is missing. Please log in again.", "error");
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete treatment!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#cfece0",
        cancelButtonColor: "#f89884",
        confirmButtonText: "Yes, delete it!",
        customClass: {
            confirmButton: "swal-confirm-btn",
            cancelButton: "swal-cancel-btn"
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/api/treatments/" + treatmentId,
                type: "DELETE",
                headers: {
                    "Authorization": "Bearer " + apiToken
                },
                success: function () {
                    Swal.fire({
                        title: "Deleted!",
                        text: "Treatment deleted successfully!",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "/treatment";
                    });
                },
                error: function (xhr) {
                    Swal.fire(
                        "Error",
                        xhr.responseJSON?.message || "Failed to delete treatment. Please try again.",
                        "error"
                    );
                }
            });
        }
    });
});
