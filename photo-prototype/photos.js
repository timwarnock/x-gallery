/* ********************************************************************************
  photos.js

  photos namespace 'photox'

******************************************************************************** */
var photox = new function() {


// private GALLERIES
var GALLERIES = false;

// public GALLERIES
this.getGalleries = function() { return GALLERIES; };
this.setGalleries = function(data) { GALLERIES = data; };

// private (get preview thumb for an exhibit)
var getPreviewImage = function(ex) {
  return 'http://avant.net/photos/service.php/'+ ex.dir.replace(/\/$/, "") +'.jpg';
};


// public (close the modal)
this.closeModalBrowser = function() {
  var modal = document.getElementById('xg_browser');
  modal.style.display = "none";
};
this.closeModalImage = function() {
  var modal2 = document.getElementById('xg_image');
  modal2.style.display = "none";
};

// public (modal browser)
this.openModalBrowser = function(name) {
getJSON('http://avant.net/photos/json/'+name,
function(err, data) {
  if (err !== null) {
    console.log('Something went wrong: ' + err);
  } else {
  var images = data.images;
  var title = data.title;
  var desc = data.desc;
  var gallery = '<div id="xg_browserContent">';
  gallery += '<span class="close" onclick="photox.closeModalBrowser()">&times;</span><div class="xg_gallery_title">' + title + '</div>';
  gallery += '<div id="xg_gallery_artwork">';
  for (var i=0; i < images.length; i++) {
    art = images[i].replace(/'/g, "\\'");;
    var img = '/photos/' + name + '/small_img_' + art;
    var imgclick = ' onclick="photox.openModalImage(\'' + name + '\',\'' + art + '\')" ';
    gallery += '<div class="xg_preview" id="xg_' + art + '" style="background-image:url(' + img + ');"' + imgclick + '>';
    gallery += '<div class="xg_preview_screen"><div class="xg_preview_title">' + art + '</div></div></div>';
  }
  gallery += '<div class="xg_gallery_desc">'+ desc +'</div>';
  gallery += '</div></div>';
  document.getElementById('xg_browser').style.display = "block";
  document.getElementById('xg_browserContent_wrap').innerHTML = gallery;
}})};

// public (modal image viewer)
// todo arrow buttons, movie player
this.openModalImage = function(name, art) {
  var imghtml = '<span class="close" onclick="photox.closeModalImage()">&times;</span><div class="xg_gallery_title">' + name + '</div>';
  imghtml += '<div id="xg_gallery_artwork">';
  var img = '/photos/' + name + '/img_' + art;
  var raw_url = '/photos/' + name + '/' + art;
  var imgclick = ' onclick="photox.closeModalImage()" ';
  imghtml += '<img src="' + img + '"' + imgclick + '>';
  imghtml += '<div class="xg_gallery_desc">' + art + '</div>';
  imghtml += '<div class="xg_image_download"><a href="'+ raw_url +'" target="_new">download</a></div>';
  imghtml += '</div>';
  document.getElementById('xg_image').style.display = "block";
  document.getElementById('xg_imageContent').innerHTML = imghtml;
};

// private (fetch JSON object from URL)
var getJSON = function(url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.responseType = 'json';
    xhr.onload = function() {
      var status = xhr.status;
      if (status === 200) {
        var resjson = xhr.response;
        if (typeof resjson == "string") {
          resjson = JSON.parse(xhr.response);
        }
        callback(null, resjson);
      } else {
        callback(status, xhr.response);
      }
    };
    xhr.send();
};


// public (create the gallery browser) 
// photox.gallery(id)
// id refers to the dom element where you want to create the gallery
// e.g.,
//      <div id="exihibits"></div>
//      <script>
//        photox.gallery('exhibits');
//      </script>
this.gallery = function(exid) {
  var artw = this.getGalleries();
  if (artw) {
    this.openGalleries(exid);
  } else {
    console.log('Fetching json');
    var self = this;
    getJSON('http://avant.net/photos/json',
    function(err, data) {
      if (err !== null) {
        console.log('Something went wrong: ' + err);
      } else {
        self.setGalleries.apply(self, [data]);
        self.openGalleries.apply(self,[exid]);
      }
    });
  }
};
this.openGalleries = function(exid) {
  var msg = '';
  for (var i = 0; i<GALLERIES.length; i++) {
      var ex = GALLERIES[i];
      var preview = getPreviewImage(ex);
      msg += '<div class="photox xg_preview xg_exhibit" id="xg_' + ex.dir + '" style="background-image:url(' + preview + ');" onclick="photox.openModalBrowser(\'' + ex.dir + '\');">';
      msg += '<div class="xg_preview_screen"><div class="xg_preview_title">' + ex.title + '</div></div></div>';
  }
  msg += '<div id="xg_browser" class="photox modal"><div id="xg_browserContent_wrap"></div></div>';
  msg += '<div id="xg_image" class="photox modal2"><div id="xg_imageContent"></div></div>';
  document.getElementById(exid).innerHTML = msg;
};
this.preload = function(statusid,statusmsg) {
  var artw = this.getGalleries();
  if (artw && statusid) {
    document.getElementById(statusid).innerHTML = statusmsg;
  } else {
    console.log('Fetching json');
    var self = this;
    getJSON('http://avant.net/photos/json',
    function(err, data) {
      if (err !== null) {
        console.log('Something went wrong: ' + err);
      } else {
        self.setGalleries.apply(self, [data]);
        document.getElementById(statusid).innerHTML = statusmsg;
      }
    });
  }
};


}; //end photox namespace



// event listeners
// TODO make better (DRY)
window.onclick = function(event) {
  var modal = document.getElementById('xg_browser');
  if (event.target == modal) {
    photox.closeModalBrowser();
  }
  var modal2 = document.getElementById('xg_image');
  if (event.target == modal2) {
    photox.closeModalImage();
  }
  var clicke = document.getElementById('xg_gallery_artwork');
  if (event.target == clicke) {
    photox.closeModalBrowser();
  }
};


