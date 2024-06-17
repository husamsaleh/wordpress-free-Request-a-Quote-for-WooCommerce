jQuery(document).ready(function ($) {
  $(".afq-btn").on("click", function () {
    var product_id = $(this).data("product-id");
    var product_name = $(this).data("product-name");
    var variation_id = $("#afq-variation").val();
    var variation_name = $("#afq-variation option:selected").text();
    var product_url = window.location.href;

    if ($("#afq-variation").length && !variation_id) {
      alert("Please select a variation.");
      return;
    }

    $("#afq-product-id").val(product_id);
    $("#afq-product-name").val(product_name);
    $("#afq-variation-id").val(variation_id);
    $("#afq-variation-name").val(variation_name);
    $("#afq-product-url").val(product_url);

    // Update the modal content
    $("#display-product-name").text(product_name);
    $("#display-variation-name").text(variation_name);

    $(".afq-modal").fadeIn();
  });

  $(".afq-close").on("click", function () {
    $(".afq-modal").fadeOut();
  });

  $(document).on("wpcf7mailsent", function (event) {
    alert("Request sent!");
    $(".afq-modal").fadeOut();
  });

  $(document).on("click", function (event) {
    if ($(event.target).is(".afq-modal")) {
      $(".afq-modal").fadeOut();
    }
  });

  // Ensure fields are populated before form submission
  $(document).on("submit", ".wpcf7-form", function () {
    var product_id = $(".afq-btn").data("product-id");
    var product_name = $(".afq-btn").data("product-name");
    var variation_id = $("#afq-variation").val();
    var variation_name = $("#afq-variation option:selected").text();
    var product_url = window.location.href;

    $("#afq-product-id").val(product_id);
    $("#afq-product-name").val(product_name);
    $("#afq-variation-id").val(variation_id);
    $("#afq-variation-name").val(variation_name);
    $("#afq-product-url").val(product_url);

    // Log the values to ensure they are set correctly
    console.log("Product ID: " + product_id);
    console.log("Product Name: " + product_name);
    console.log("Variation ID: " + variation_id);
    console.log("Variation Name: " + variation_name);
    console.log("Product URL: " + product_url);
  });
});
