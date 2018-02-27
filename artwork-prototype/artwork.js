/* ********************************************************************************
  artwork.js

  artwork namespace 'xga'

******************************************************************************** */
var xga = new function() {


// private ARTWORK
var ARTWORK = false;

// public ARTWORK
this.getArtwork = function() { return ARTWORK; };
this.setArtwork = function(data) { ARTWORK = data; };



this.addCSS = function() {
  var head = document.head;
  var link = document.createElement("link");
  link.type = "text/css";
  link.rel = "stylesheet";
  link.href = "http://avant.net/artwork/artwork.css";
  head.appendChild(link);
};


// private (get preview thumb for an exhibit)
var getPreviewImage = function(ex) {
  for (var art in ex.artwork) {
    if (typeof ex.artwork[art] !== 'function') {
      return 'http://avant.net/artwork/' + ex.name + '/small_img_' + art;
    }
  }
};


// private (test to see if video is supported)
var supports_video = function() {
  var isMSIE = /*@cc_on!@*/0;
  if (isMSIE &&
    !(navigator.userAgent.match(/iPhone/i)) && 
    !(navigator.userAgent.match(/iPad/i)) && 
    !(navigator.userAgent.match(/iPod/i)) ) {
    return true;
  }
  return !!document.createElement('video').canPlayType;
};

function videoize(name, art) {
  var artwork = ARTWORK[name].artwork[art];
  if (supports_video() ) {
    (function () {
    var el = document.getElementById('xg_gallery_image');
    var baseURL = 'http://avant.net/artwork/' + name + '/';
    var newHTML = '';
    newHTML += '<video id="xgaVid" poster="'+ baseURL + art +'" loop>';
    if ('webm' in artwork) {
      newHTML += '<source src="' + baseURL + artwork.webm +'" type="video/webm" />';
    }
    if ('mp4' in artwork) {
      newHTML += '<source src="' + baseURL + artwork.mp4 +'" type="video/mp4" />';
    }
    if ('ogv' in artwork) {
      newHTML += '<source src="' + baseURL + artwork.ogv +'" type="video/ogv" />';
    }
    if ('swf' in artwork) {
      newHTML += '<object type="application/x-shockwave-flash" data="'+ baseURL + artwork.swf +'">'+
      '<param name="movie" value="'+ baseURL + artwork.swf +'" />'+
      '<img src="'+ baseURL + art +'" alt="No video playback" title="No video playback" />'+
      '</object>'; 
    }
    newHTML += '</video>';
    newHTML += '<div class="xg_gallery_desc">' + artwork.desc + '</div>';
    el.innerHTML = newHTML;

    var video = document.getElementById("xgaVid");
    // chrome bug, video doesn't autostart without controls
    video.setAttribute('controls', 'true');
    video.removeAttribute('controls');
    video.load();
    video.play();
    }()
    );
  }
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

// public (browse all images in exhibit)
this.openModalBrowser = function(name) {
  var artwork = ARTWORK[name].artwork;
  var title = ARTWORK[name].title;
  var desc = ARTWORK[name].desc;
  var gallery = '<div id="xg_browserContent">';
  gallery += '<span class="close" onclick="xga.closeModalBrowser()">&times;</span><div class="xg_gallery_title">' + title + '</div>';
  gallery += '<div id="xg_gallery_artwork">';
  for (var art in artwork) {
    if (typeof artwork[art] !== 'function') {
      var img = 'http://avant.net/artwork/' + name + '/small_img_' + art;
      var imgclick = ' onclick="xga.openModalImage(\'' + name + '\',\'' + art + '\')" ';
      gallery += '<div class="xg_preview" id="xg_' + art + '" style="background-image:url(' + img + ');"' + imgclick + '>';
      gallery += '<div class="xg_preview_screen"><div class="xg_preview_title">' + artwork[art].title + '</div></div></div>';
    }
  }
  gallery += '<div class="xg_gallery_desc">'+ desc +'</div>';
  gallery += '</div></div>';
  document.getElementById('xg_browser').style.display = "block";
  document.getElementById('xg_browserContent_wrap').innerHTML = gallery;
};

// public (modal image viewer)
// todo arrow buttons, movie player
this.openModalImage = function(name, art) {
  var artwork = ARTWORK[name].artwork[art];
  var imghtml = '<span class="close" onclick="xga.closeModalImage()">&times;</span><div class="xg_gallery_title">' + artwork.title + '</div>';
  imghtml += '<div id="xg_gallery_image">';
  var img = 'http://avant.net/artwork/' + name + '/img_' + art;
  var raw_url = 'http://avant.net/artwork/' + name + '/' + art;
  var imgclick = ' onclick="xga.closeModalImage()" ';
  imghtml += '<img src="' + img + '"' + imgclick + '>';
  imghtml += '<div class="xg_gallery_desc">' + artwork.desc + '</div>';
  imghtml += '<div class="xg_image_download"><a href="'+ raw_url +'" target="_blank">download</a></div>';
  imghtml += '</div>';
  document.getElementById('xg_image').style.display = "block";
  document.getElementById('xg_imageContent').innerHTML = imghtml;
  if ('mp4' in artwork) {
    videoize(name, art);
  }
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

// public (open single exhibit in modal browser)
//   + exid is the div#id to write the browser
//   + exname is the name of the exhibit to open
this.exhibit = function(exid,exname) {
  var artw = this.getArtwork();
  if (exname in artw) {
    this.openModalBrowser(exname);
  } else {
    console.log('Fetching json');
    var self = this;
    getJSON('http://avant.net/artwork/json',
    function(err, data) {
      if (err !== null) {
        console.log('Something went wrong: ' + err);
      } else {
        self.setArtwork.apply(self, [data]);
        var msg = '';
        msg += '<div id="xg_browser" class="modal"><div id="xg_browserContent_wrap"></div></div>';
        msg += '<div id="xg_image" class="modal2"><div id="xg_imageContent"></div></div>';
        document.getElementById(exid).innerHTML = msg;
        self.openModalBrowser.apply(self, [exname]);
      }
    });
  }
};

// public (create the exhibit browser) 
// xga.gallery(id)
// id refers to the dom element where you want to create the gallery
// e.g.,
//      <div id="exihibits"></div>
//      <script>
//        xga.gallery('exhibits');
//      </script>
this.gallery = function(exid) {
  var artw = this.getArtwork();
  if (artw) {
    this.openExhibits(exid);
  } else {
    console.log('Fetching json');
    var self = this;
    getJSON('http://avant.net/artwork/json',
    function(err, data) {
      if (err !== null) {
        console.log('Something went wrong: ' + err);
      } else {
        self.setArtwork.apply(self, [data]);
        self.openExhibits.apply(self,[exid]);
      }
    });
  }
};
this.openExhibits = function(exid) {
  artw = ARTWORK;
  var msg = '';
  for (var key in artw) {
    if (typeof artw[key] !== 'function') {
      var ex = artw[key];
      var preview = getPreviewImage(ex);
      msg += '<div class="xga xg_preview xg_exhibit" id="xg_' + key + '" style="background-image:url(' + preview + ');"';
      msg += ' onclick="xga.openModalBrowser(\'' + key + '\');">';
      msg += '<div class="xg_preview_screen"><div class="xg_preview_title">' + ex.title + '</div></div></div>';
    }
  }
  msg += '<div id="xg_browser" class="xga modal"><div id="xg_browserContent_wrap"></div></div>';
  msg += '<div id="xg_image" class="xga modal2"><div id="xg_imageContent"></div></div>';
  document.getElementById(exid).innerHTML = msg;
};
this.preload = function(statusid,statusmsg) {
  var artw = this.getArtwork();
  if (artw && statusid) {
    document.getElementById(statusid).innerHTML = statusmsg;
  } else {
    console.log('Fetching json');
    var self = this;
    getJSON('http://avant.net/artwork/json',
    function(err, data) {
      if (err !== null) {
        console.log('Something went wrong: ' + err);
      } else {
        self.setArtwork.apply(self, [data]);
        document.getElementById(statusid).innerHTML = statusmsg;
      }
    });
  }
};


}; //end xga


// event listeners
// TODO make better (DRY)
window.onclick = function(event) {
  var modal = document.getElementById('xg_browser');
  if (event.target == modal) {
    xga.closeModalBrowser();
  }
  var modal2 = document.getElementById('xg_image');
  if (event.target == modal2) {
    xga.closeModalImage();
  }
  var clicke = document.getElementById('xg_gallery_artwork');
  if (event.target == clicke) {
    xga.closeModalBrowser();
  }
};


