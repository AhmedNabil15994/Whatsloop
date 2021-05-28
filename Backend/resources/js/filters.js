import Vue from "vue";

export default (value) => {
  const date = new Date(value)
  return date.toLocaleString(['en-US'],{month:'short',day:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'})
}

Vue.filter('uppercase',function(v) {
    return v.toUpperCase();
  });
  
  Vue.filter("reversing",function (v) {
    return v.split("").reverse().join("");
  });

  Vue.filter("maxChars",function (v,max,more) {
    return v.substring(0,max) + more;
  });

  Vue.filter("removeAst",function (v) {

    if(v.split('').every(char => char === v[0])) {
      return v;
    } else {
      var bold2 = /\*(.*)\*/gm;
      var html2 = v.replace(bold2, '<bold>$1</bold>');            
      return html2;
      /*if(v.includes("**"))
      {
        var bold = /\*\*(.*?)\*\*gm;
        var html = v.replace(bold, '*<bold>$1</bold>*');            
        return html;
      }
      else {
        var bold2 = /\*(.*?)\*gm;
        var html2 = v.replace(bold2, '<bold>$1</bold>');            
        return html2;
      }*/
    }
  });

  Vue.filter("readLinks",function (text) {
    if(!text.includes("src=")) {
      var urlRegex = /(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/gi;
      return text.replace(urlRegex, function(url) {
        return '<a target="_blank" href="' + url + '">' + url + '</a>';
      });
    } else {
      return text;
    }

  });