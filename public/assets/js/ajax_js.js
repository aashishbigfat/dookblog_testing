$('.categories').select2({
    placeholder: 'Select/Create Categories',
    tags:true,
    ajax: {
        url: "/categories_list_ajax",
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.name,
                        id: item.name
                    }
                })
            };
        },
        cache: true
    }
});
// Tags
$('.tags').select2({
    placeholder: 'Create Tags',
    tags:true,
    ajax: {
        url: "/tags_list_ajax",
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.name,
                        id: item.name
                    }
                })
            };
        },
        cache: true
    }
});

// slug
    // var Text = $('#title').val();
    // Text = Text.toLowerCase();
    // var aa = toTitleCase(Text);
    // $('#title').val(aa);
  // $('#title').on("change keyup paste click", function() {
  //   var Text = $(this).val();
  //   Text = Text.toLowerCase();
  //   var aa = toTitleCase(Text);
  //   //console.log(aa);
  //   $('#title').val(aa);
  //   Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');
  //   $('#slug').val(Text);


    /////

    // var $this = $(this);
    // var val = $this.val();
    // var valLength = val.length;
    // var maxCount = $this.attr('maxlength');
    // if(valLength>maxCount){
    //     $this.val($this.val().substring(0,maxCount));
    // }
    // if(valLength==maxCount){
    //     $('.title_ch_error').html('You are allowed only 60 Character!');
    // }else{
    //     $('.title_ch_error').html('');
    // }
  //});

    // function toTitleCase(str) {
    //     var lcStr = str.toLowerCase();
    //     return lcStr.replace(/(?:^|\s)\w/g, function(match) {
    //         return match.toUpperCase();
    //     });
    // }



  //page url
  // $('#title').on("change keyup paste click", function() {
  //   var Text = $(this).val();
  //   Text = Text.toLowerCase();
  //   Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');
  //   $('#pageUrl').html('<a href="https://www.dookinternational.com/blog/'+Text+'">https://www.dookinternational.com/blog/'+Text+'</a>');
  // });
  $('#slug').on("change keyup paste click", function() {
    var Text = $(this).val();
    Text = Text.toLowerCase();
    Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');
    $('#pageUrl').html('<a href="https://www.dookinternational.com/blog/'+Text+'">https://www.dookinternational.com/blog/'+Text+'</a>');
  });

  // slug
  $('#edit_name').on("change keyup paste click", function() {
    var Text = $(this).val();
    Text = Text.toLowerCase();
    Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');
    $('#edit_slug').val(Text);
  });

//Short description char limit
var $txtLenLeft = $('.totalChar').html(120); // lets cache this
var maxLen = 120;
$('#short_description').keydown(function(e){
   var Length = $(this).val().length;
   var AmountLeft = maxLen - Length;
   $txtLenLeft.html(AmountLeft);
   if(Length >= maxLen && e.keyCode != 8){
      e.preventDefault(); // will cancel the default action of the event
   }
});
// $('#short_description').keypress(function(e) {
//     var tval = $('#short_description').val(),
//         tlength = tval.length,
//         set = 120,
//         remain = parseInt(set - tlength);
//     $('.totalChar').text(remain);
//     if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
//         $('#short_description').val((tval).substring(0, tlength - 1));
//         $('.totalChar').text(remain+tlength);
//     }
// })