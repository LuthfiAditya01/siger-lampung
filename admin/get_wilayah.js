$("#provinsi").on("change", function () {
  var id = $(this).val();
  $("#kabupaten").html('<option value="">Loading...</option>');
  $.get("get_wilayah.php", { tipe: "kabupaten", id: id }, function (data) {
    var options = '<option value="">-- Pilih Kabupaten --</option>';
    data.forEach(function (d) {
      options += `<option value="${d.id_kabupaten}">${d.nama_kabupaten}</option>`;
    });
    $("#kabupaten").html(options);
  });
});

$("#kabupaten").on("change", function () {
  var id = $(this).val();
  $("#kecamatan").html('<option value="">Loading...</option>');
  $.get("get_wilayah.php", { tipe: "kecamatan", id: id }, function (data) {
    var options = '<option value="">-- Pilih Kecamatan --</option>';
    data.forEach(function (d) {
      options += `<option value="${d.id_kecamatan}">${d.nama_kecamatan}</option>`;
    });
    $("#kecamatan").html(options);
  });
});

$("#kecamatan").on("change", function () {
  var id = $(this).val();
  $("#kelurahan").html('<option value="">Loading...</option>');
  $.get("get_wilayah.php", { tipe: "kelurahan", id: id }, function (data) {
    var options = '<option value="">-- Pilih Kelurahan --</option>';
    data.forEach(function (d) {
      options += `<option value="${d.id_kelurahan}">${d.nama_kelurahan}</option>`;
    });
    $("#kelurahan").html(options);
  });
});
