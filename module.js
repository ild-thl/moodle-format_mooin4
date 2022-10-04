// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * format_mooin4_renderer
 *
 * @package    format_mooin4
 * @author     Rodrigo Brandão <https://www.linkedin.com/in/brandaorodrigo>
 * @copyright  2020 Rodrigo Brandão <rodrigo.brandao.contato@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.format_mooin4 = M.format_mooin4 || {
    ourYUI: null,
    numsections: 0,
    instance: function() {
      console.log("Arguments", arguments)
      this.init( arguments );
      if (! arguments[3]) {
          Chapters.init(arguments[2]);
      }
      Y.all('.showHideCont a').each( function(node) {
          var _parent = node.ancestor('.showHideCont');
          node.on('click', function() {
              var target = Y.one('#t_' + this.get('rel'));
              this.setStyle('display', 'none');
              if (this.hasClass('plus')) {
                  target.setStyle('display', 'block');
                  _parent.one('.minus').setStyle('display', 'inline');
                  _parent.setStyle('cssFloat', 'right');
              } else {
                  target.setStyle('display', 'none');
                  _parent.one('.plus').setStyle('display', 'inline');
                  _parent.setStyle('cssFloat', 'left');
              }
          });
      });
      
  },
  //global settings
  global: function() {
      this.init( arguments );
      Util.show_hide(Y.one('#expandableTreeContainer a'));
  },

};

M.format_mooin4.init = function( Y, numsections, currentsection, courseid) {

    this.ourYUI = Y;
    this.numsections = parseInt(numsections);
    console.log('currentSection', currentsection);
    console.log('Numsection', this.numsections);
    /* document.getElementById('mooin4ectioncontainer').style.display = 'table'; */
   /*  U.img = args[0].img;
      U.str = args[0].str;
      config = args[1];
      var _form = Y.one('form#adminsettings'); //general (global) settings
      if (! _form ) {
          _form = Y.one('form.mform'); // instance config
      }
      if (_form) {
          _form.on('submit', function( e ) {
              if (! Links.validate()) {
                  e.preventDefault();
              }
              return false;
          });
    } */
    window.onload = function main() {
        var slider = new Slider(document.querySelector('#slider'),{
            items: this.numsections,
            responsive: true,
            mobile : {
                items : 1
            },
            tablet : {
                width : 768,
                items : 3,
            },
            desktop : {
                width : 1040,
                items : 5,
            },
            deviceChanged : function (e) {
                console.log(e);
            },
            navChanged : function (e) {
                console.log(e);
            },
            /* update_nav: function(e) {
              console.log(e);
            },
            update_mooin4: function(e) {
              console.log(e);
            } */
        })
        // slider.update_nav();
        document.addEventListener('DOMContentLoaded', main)
    }
    var extend = function (source, target) {
        if(typeof source == 'object'){
            for (var property in source){
                if(!target[property]) {
                    target[property] = source[property];
                }
            }
        }
        return target;
    }
    
    
    function Slider(el, options) {
        var self = this,
            carousel_container,
            slides,
            prev_button,
            next_button,
            carousel_nav,
            current_nav,
            steps,
            current_step,
            item_per_step,
            /* play_timer,
            resize_timer, */
            windowState,
            listeners = [];


        var defaults = {
            /* autoplay: false,
            interval: 4000, */
            createNav: true,
            items: this.numsections,
            responsive: false,
            mobile: {
            items: 2
            },
            tablet: {
            width: 768,
            items: 6
            },
            desktop: {
            width: 1024,
            items: 8
            },
            maxHeight: undefined,
            deviceChanged: undefined,
            navchanged: undefined
        };

        // wrap function
        function wrap(element, start, end) {
            var html = start + element.outerHTML + end;
            element.outerHTML = html;
        }

        // Create Navigation under carousel
        function create_nav() {
            carousel_nav = document.createElement('ul');
            carousel_nav.classList.add('carousel__nav');
            create_nav_links();
            el.appendChild(carousel_nav);
        }

        // Update the carousel nav 
        function update_nav() {
            var links_count = carousel_nav.querySelectorAll('li').length;
            console.log('Links_count Update Nav', links_count);
            console.log('Steps Update Nav', steps);
            if (links_count !== steps) {
              carousel_nav.innerHTML = '';
              create_nav_links();
              raiseEvent({
                type: 'navchanged',
                currentStep: current_step,
                itemPerStep: item_per_step,
                steps: steps
              });
            }
        }
        //Get index for a specific element in carousel
        function getIndex(element) {
          console.log("Get Index", element);
            var nodes = element.parentNode.childNodes;
            for (var i = 0; i < nodes.length; i++) {
              if (nodes[i] == element) {
                return i;
              }
            }
            return -1;
        }

        //Create the Navigation Link
        function create_nav_links() {
            for (var i = 0; i < steps; i++) {
              var li = document.createElement('li');
              var nav_link = document.createElement('a');
              nav_link.setAttribute('href', '#');
        
              nav_link.addEventListener('click', function(e) {
                e.preventDefault();
        
                var index = getIndex(this.parentElement);
                moveTo(index);
        
              });
              li.appendChild(nav_link);
              carousel_nav.appendChild(li);
            }
        }

        //Get The Current left Slide
        function get_current_left_slides() {

            if (current_step == 0) {
              return 0;
            } else if (current_step == steps - 1) {
              var previous_step_slide_total = (current_step - 1) * item_per_step, // 8
                slides_left = (slides.length - current_step * item_per_step), // 1
                result = previous_step_slide_total + slides_left; // --> 9
                console.log('Step', steps);
                console.log('current step', current_step);
                console.log('previous step slide total', previous_step_slide_total);
                console.log('slides left', slides_left);
                console.log('result', result);
              return result;
            } else {
              var result = current_step * item_per_step;
              return result;
            }
        }

        //Get slide to move back of forward
        function get_slides_to_move(step) {

            // back
            if (step < current_step) {
        
              if (step == steps - 2) {
                // ex slides.length  = 13 | steps 4 | step 2
                var slide_total = slides.length, // 25
                  slides_left = (slides.length - step * item_per_step), // 7
                  slides_to_move = slide_total - slides_left; // --> 18
                console.log('slide total', slide_total);
                console.log('slide left', slides_left);
                console.log('slide to move', slides_to_move);
                return slides_to_move;
              } else {
                console.log('Step * item_per_step back', step * item_per_step);
                return step * item_per_step;
              }
        
            }
            // forward
            if (step > current_step) {
        
              if (step == steps - 1) {
                // ex slides.length  = 13 | steps 4 | step 3
                var previous_step_slide_total = (step - 1) * item_per_step, // 8
                slides_left = (slides.length - step * item_per_step), // 1
                slides_to_move = previous_step_slide_total + slides_left; // --> 9
                console.log('Slides to move forward', slides_to_move);

                return slides_to_move;
              } else {
                console.log('Step * item_per_step forward', step * item_per_step);
                return step * item_per_step;
              }
        
            }
        
            return 0;
        }

        // Resize the Carousel items
        function onResize() {
                    
            // repositionne les slides sans animation
            var percentage = 100 / item_per_step,
              x = item_per_step > 1 ?
              percentage * get_current_left_slides() :
              current_step * percentage;
        
            carousel_container.style.transition = 'none';
            carousel_container.style.transform = 'translateX(-' + x + '%)';
            update_nav();
            update_mooin4();
            setTimeout(function() {
              carousel_container.style.removeProperty('transition');
            }, 500);
        
            if (window.innerWidth < options.tablet.width) {
              if (windowState != 'mobile') {
                windowState = 'mobile';
                if (options.responsive) setItems(options.mobile.items);
                raiseEvent({
                  type: 'devicechanged',
                  device: 'mobile'
                });
              }
            } else if (window.innerWidth < options.desktop.width) {
              if (windowState != 'tablet') {
                windowState = 'tablet';
                if (options.responsive) setItems(options.tablet.items);
                raiseEvent({
                  type: 'devicechanged',
                  device: 'tablet'
                });
              }
            } else if (window.innerWidth > options.desktop.width) {
              if (windowState != 'desktop') {
                windowState = 'desktop';
                if (options.responsive) setItems(options.desktop.items);
                raiseEvent({
                  type: 'devicechanged',
                  device: 'desktop'
                });
              }
            }
        }
        // update_mooin4() was here
        // Raise an Event
        function raiseEvent(event) {
            if (typeof event == 'string') event = {
              type: event
            };
            if (!event.target) event.target = this;
            if (!event.type) throw new Error("Event object missing 'type' property.");
        
            if (listeners[event.type] instanceof Array) {
              var _listeners = listeners[event.type];
              for (var i = 0; i < _listeners.length; i++) {
                _listeners[i].call(self, event);
              }
            }
        }

        //Init function
        function init() {

            if (!el) throw new Error("'El' cannot be null");
        
            options = extend(defaults, options || {});
        
            wrap(el.querySelector('.carousel-inner'), '<div class="carousel__viewport">', '</div>');
        
            carousel_container = el.querySelector('.carousel-inner'); // .slides__slides = carousel-inner
        
            slides = el.querySelectorAll('.mooin4ection');
        
            prev_button = el.querySelector('.carousel__button--prev');
            next_button = el.querySelector('.carousel__button--next');
        
            if (options.maxHeight) {
              carousel_container.style.maxHeight = options.maxHeight + 'px';
            }
        
            if (options.deviceChanged) {
              addListener('devicechanged', options.deviceChanged);
            }
        
            if (options.navChanged) {
              addListener('navchanged', options.navChanged);
            }
        
            current_step = 0;
        
            if (options.responsive) {
              if (window.innerWidth < options.tablet.width) {
                setItems(options.mobile.items);
              } else if (window.innerWidth < options.desktop.width) {
                setItems(options.tablet.items);
              } else if (window.innerWidth > options.desktop.width) {
                setItems(options.desktop.items);
              }
            } else {
              setItems(options.items);
            }
        
            if (options.createNav) {
              create_nav();
            }
            if(options.update_nav) {
              console.log("Update Nav");
              moveTo();
              update_nav();
              update_mooin4()
            }
        
            // events
            window.addEventListener('resize', function() {
              onResize();
            });
        
            if (prev_button) {
              prev_button.addEventListener('click', function(e) {
                e.preventDefault();
                moveBack();
              });
            }
        
            if (next_button) {
              next_button.addEventListener('click', function(e) {
                  console.log('E in forward', e);
                e.preventDefault();
                moveForward();
              });
            }
        
            //
            //onResize();
            update_mooin4();
        }

        // AddListener function
        function addListener(type, listener) {
            if (typeof listeners[type] == "undefined") {
              listeners[type] = [];
            }
            listeners[type].push(listener);
        }

        // Move back function
        function moveBack() {
            if (current_step <= 0) return false;
            
            moveTo(current_step - 1);

           /*  if (currentsection <= slides.length) {
              console.log('Back',current_step );
              M.format_mooin4.show(currentsection - 1, courseid); 
          } */
        }
        // Move Forward 
        function moveForward() {
            if (current_step >= steps - 1) return false;
            moveTo(current_step + 1);

            /* if (currentsection <= slides.length) {
                console.log('Foward',current_step );
                M.format_mooin4.show(currentsection +1, courseid); 
            } */
        }

        // Set items in carousel visible
        function setItems(count) {

            item_per_step = count;
            steps = Math.ceil(slides.length / item_per_step);
            console.log('Step in set Items', steps);
            console.log('Currentsection setItems', currentsection);
            if (item_per_step > 1) {
              var slide_width = 100 / item_per_step;
              for (var i = 0; i < slides.length; i++) {
                var slide = slides[i];
                // console.log("Slide", slide);
                if (slide) {
                  slide.style.width = slide_width + '%';
                }
              }
            } else {
              for (var i = 0; i < slides.length; i++) {
                var slide = slides[i];
                if (slide) {
                  slide.style.removeProperty('width');
                }
              }
            }
        
            // repositionne les slides sans animation
            var percentage = 100 / item_per_step,
              x = item_per_step > 1 ?
              percentage * get_current_left_slides() :
              current_step * percentage;
        
            carousel_container.style.transition = 'none';
            carousel_container.style.transform = 'translateX(-' + x + '%)';
            setTimeout(function() {
                carousel_container.style.removeProperty('transition');
            }, 500);
        }
        // MoveTo () was here
        // MoveTo function
        function moveTo(step) {
          if (step < 0 || step > steps - 1) return false;
          var percentage = 100 / item_per_step,
            x = item_per_step > 1 ?
            percentage * get_slides_to_move(step) :
            step * percentage;
          carousel_container.style.transform = 'translateX(-' + x + '%)';
          current_step = step;

          console.log('Step in moveTo', step);
          console.log('Current step in Move to', current_step);
          update_mooin4();
        }
        // Update mooin4
        function update_mooin4() {

          if (prev_button) {
            if (current_step <= 0) {
              prev_button.classList.add('hidden');
            } else {
              prev_button.classList.remove('hidden');
            }
          }
          if (next_button) {
            if (current_step >= steps - 1) {
              next_button.classList.add('hidden');
            } else {
              next_button.classList.remove('hidden');
            }
          }
      
          // navs
          if (options.createNav) {
            console.log('current_step in update_mooin4', current_step);
            current_nav = carousel_nav.querySelectorAll('a')[current_step];
            var active = carousel_nav.querySelector('a.active'); //active
            if (active) {
              active.classList.remove('active');//active
            }
            current_nav.classList.add('active');//active
          }
        }

        

        init();

        return {
            addEventListener: addEventListener,
            moveBack: moveBack,
            moveForward: moveForward,
            moveTo: moveTo,
            /* pause: pause,
            play: play, */
            setItems: setItems
          }
    }
    // Bottom button variable
    var nextBottom = document.querySelector('.bottom_next');
    var prevBottom = document.querySelector('.bottom_previous');
    // First come into the course page when section egal to 1
    // NextBottom code come here...
    nextBottom.addEventListener('click', function(event) {
      console.log('Numsection', event.view.M.format_mooin4.numsections);
      var url = event.view.window.location.href;
      console.log(parseInt(url.substring(url.lastIndexOf('=') + 1)) + 1);
      console.log(event);
      var sectionInUrl = parseInt(url.substring(url.lastIndexOf('=') + 1)) + 1;
      var numSections = event.view.M.format_mooin4.numsections;
      if (sectionInUrl >= numSections) {
        M.format_mooin4.show(parseInt(currentsection) + 1, courseid);
        currentsection++;
        nextBottom.classList.add('remove_pointer');
        nextBottom.classList.add('disable_button');
      } else {
          nextBottom.classList.remove('remove_pointer');
          nextBottom.classList.remove('disable_button');
          M.format_mooin4.show(parseInt(currentsection) + 1, courseid);
          currentsection++;
      }
      if (currentsection > 1) {
          prevBottom.classList.remove('remove_pointer');
          prevBottom.classList.remove('disable_button');
      }
      // nextBottom.classList.add('hidden');
      console.log('Type of current section', typeof currentsection);
      console.log("Current Section next", currentsection);
      // currentsection++;
    });
    // Previous_bottom code here ...
    prevBottom.addEventListener('click', function(event) {
      var url = event.view.window.location.href;
      var sectionInUrl = parseInt(url.substring(url.lastIndexOf('=') + 1));
      if (sectionInUrl - 1 > 1) {
        prevBottom.classList.remove('remove_pointer');
        prevBottom.classList.remove('disable_button');
        M.format_mooin4.show(parseInt(currentsection) - 1, courseid);
        currentsection--;
      } else {
        M.format_mooin4.show(parseInt(currentsection) - 1, courseid);
        currentsection--;
        prevBottom.classList.add('remove_pointer');
        prevBottom.classList.add('disable_button');
      }
      var numSections = event.view.M.format_mooin4.numsections;
      if (currentsection < numSections) {
          nextBottom.classList.remove('remove_pointer');
          nextBottom.classList.remove('disable_button');
      }
      // Currentsection--;
    });

    var findHash = function(href) {
        var id = null;
        if (href.indexOf('#section=') !== 0) {
            var split = href.split('#section=');
            id = split[1];
        }
        return id;
    };

    var hash = findHash(window.location.href);
    if (hash) {
        currentsection = hash;
    }

    if (currentsection) {
        M.format_mooin4.show(currentsection, courseid);
    }

    Y.delegate('click', function(e) {
      var href = e.currentTarget.get('href');
      currentsection = findHash(href);
      M.format_mooin4.show(currentsection, courseid);
    }, '[data-region="drawer"]', '[data-type="30"]');
};

M.format_mooin4.hide = function() {
    for (var i = 1; i <= this.numsections; i++) {
        if (document.getElementById('mooin4ection-' + i) != undefined) {
            var mooin4ection = document.getElementById('mooin4ection-' + i);
            mooin4ection.setAttribute('class', mooin4ection.getAttribute('class').replace('sectionvisible', ''));
            document.getElementById('section-' + i).style.display = 'none';
        }
    }
};

M.format_mooin4.show = function(id, courseid) {
    this.hide();
    if (id > 0) {
        document.location.hash = '#section=' + id ;
        var mooin4ection = document.getElementById('mooin4ection-' + id);
        var currentsection = document.getElementById('section-' + id);
        var sectionvisible = document.getElementsByClassName('sectionvisible');
        var parentDOM = document.getElementById("slider_inner");
        console.log('Mooin4section', mooin4ection);
        console.log('sectionvisible', sectionvisible);
        if (mooin4ection && currentsection) {
            mooin4ection.setAttribute('class', mooin4ection.getAttribute('class') + ' sectionvisible');
            currentsection.style.display = 'block';
            document.cookie = 'sectionvisible_' + courseid + '=' + id + '; path=/';
            M.format_mooin4.h5p();
        }
        var testTarget = parentDOM.getElementsByClassName("sectionvisible")[0];
        console.log('Show sectionvisible', testTarget);
        var section_check = currentsection.attributes.id.value.split("-");
        var nextBottom = document.querySelector('.bottom_next');
        var prevBottom = document.querySelector('.bottom_previous');
        // First come into the course page when section egal to 1
        console.log('CCurrentsection', currentsection);
        // Check if the visible section is the first one
        if (section_check[1] == 1) {
          prevBottom.classList.add('remove_pointer');
          prevBottom.classList.add('disable_button');
        } else {
          prevBottom.classList.remove('remove_pointer');
          prevBottom.classList.remove('disable_button');
        }
        // Check if the visible section is the last one
        if (section_check[1] == this.numsections) {
          nextBottom.classList.add('remove_pointer');
          nextBottom.classList.add('disable_button');
        } else {
          nextBottom.classList.remove('remove_pointer');
          nextBottom.classList.remove('disable_button');
        }
    }
};

M.format_mooin4.h5p = function() {
    window.h5pResizerInitialized = false;
    var iframes = document.getElementsByTagName('iframe');
    var ready = {
        context: 'h5p',
        action: 'ready'
    };
    for (var i = 0; i < iframes.length; i++) {
        if (iframes[i].src.indexOf('h5p') !== -1) {
            iframes[i].contentWindow.postMessage(ready, '*');
        }
    }
};
