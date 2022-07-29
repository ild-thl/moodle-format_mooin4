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
                items : 4,
            },
            desktop : {
                width : 1040,
                items : 6,
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
    
    console.log(' This is Y', Y);
    // Bottom button variable
    var next_bottom = document.querySelector('.bottom_next'); // const
    var prev_bottom = document.querySelector('.bottom_previous');
    // var home_bottom = document.querySelector('.bottom_home');
    
    // Next_bottom code come here...
    next_bottom.addEventListener('click', function(){
        if (currentsection >= this.numsections -1) {
          next_bottom.classList.add('remove_pointer');
           
        } else {
          next_bottom.classList.remove('remove_pointer');
          M.format_mooin4.show(currentsection + 1, courseid);
          currentsection++;
        }
        // next_bottom.classList.add('hidden');
        console.log('Type of current section', typeof currentsection);
        console.log("Current Section next", currentsection);
        // currentsection++;
         
    });
    // Previous_bottom code here ...
    prev_bottom.addEventListener('click', () => {
        if (currentsection <= 1) {
          prev_bottom.classList.add('remove_pointer');
            
        } else {
          prev_bottom.classList.remove('remove_pointer');
          M.format_mooin4.show(currentsection - 1, courseid);
          currentsection--;
        }
       
        console.log("Current Section Previous", currentsection);
        // currentsection--; 
    });
    // Home_bottom code come here...
    /* home_bottom.addEventListener('click', () => {
        location.assign('http://localhost/moodle/course/format/mooin4/infos.php?id=' + courseid);
    }); */

    var findHash = function (href) {
      console.log('href Hash', href);
        var id = null;
        if (href.indexOf('#section=') !== 0) {
            var split = href.split('#section=');
            id = split[1];
        }
        return id;
    };

    var hash = findHash(window.location.href);
    // console.log('Hash', hash);
    if (hash) {
        currentsection = hash;
    }

    if (currentsection) {
        M.format_mooin4.show(currentsection, courseid);
    }

    Y.delegate('click', function (e) {
       
        var href = e.currentTarget.get('href');
        currentsection = findHash(href);
        M.format_mooin4.show(currentsection, courseid)
    }, '[data-region="drawer"]', '[data-type="30"]');

    // Table of content configuration come here!!!!
    var Chapters = {
      table: null,
      chap_enable: null,
      chap_count: null,
      subchap_count: null,
      subchap_enable: null,
      chap_container: null,
      chapterBg: "#DFDFDF",
      subChapterBg: "#FFA7FF",
      topicBg: "#FFFF91",
      sectionCounter: 0,
      sectionNames: [],
      isEditingName: false,
      init: function( sectionNames ) {
        this.sectionNames = sectionNames;
        this.table = Y.one('#chaptersTableContainer table tbody');
        this.chap_enable = Y.one('input#id_config_chapEnable');
        //this.subchap_enable = Y.one('input#id_config_subChapEnable');
        this.chap_container = Y.one('#chaptersContainer');
        this.chap_count = Y.one('#chaptersCount');
        this.subchap_count = Y.one('#subChaptersCount');
        var self = this;
        Y.one('#chap-enable').on('click', function() {
            var newValue = '';
            if (self.chap_enable.get('value') === '1') {
                this.one('img').set('src', U.img.show);
                self.chap_container.hide();
                Y.all('.cm-chapter-enable').hide();
                newValue = 0;
            } else {
                this.one('img').set('src', U.img.hide);
                self.chap_container.show();
                Y.all('.cm-chapter-enable').show();
                newValue = 1;
            }
            self.chap_enable.set('value', newValue);
            config.chapEnable = newValue;
            self.draw();
        });
        Y.one('#subchap-enable').on('click', function() {
          var newValue = '', valueChanged = false;
          if (self.subchap_enable.get('value') === '1') {
              this.one('img').set('src', U.img.show);
              newValue = 0;
              Y.all('.cm-subchapter-enable').hide();
              valueChanged = true;
          } else {
              if (confirm(U.str.warningsubchapenable)) {
                  this.one('img').set('src', U.img.hide);
                  Y.one('.cm-subchapter-enable').show();
                  newValue = 1;
                  valueChanged = true;
              }
          }
          if (valueChanged) {
              self.subchap_enable.set('value', newValue);
              config.subChapEnable = newValue;
              config.subChaptersCount = config.chapters.length;
              self.subchap_count.set('value', config.subChaptersCount);
              self.resetSubchapterGroupings().draw();
          }
      });
        this.listenInputs();
        this.enableEdits(); //listeners for editing chapter and subchapter names 
        this.draw();
      },
      listenInputs: function () {
        var self = this;
            this.chap_count.on('keypress', function( e ) {
                if ( e.keyCode === 13 ) {
                    e.preventDefault();
                    self.changeChapterNo();
                    return false;
                }
            });
            this.subchap_count.on('keypress', function( e ) {
                if ( e.keyCode === 13 ) {
                    e.preventDefault();
                    self.changeSubChapterNo();
                    return false;
                }
            });
            Y.one('#btn-change-chap-no').on('click', function( e ) {
                self.changeChapterNo();
            });
            Y.one('#btn-change-subchap-no').on('click', function( e ) {
                self.changeSubChapterNo();
            });
            Y.one('#btn-default-grouping').on('click', function( e ) {
                self.defaultGrouping(false).draw();
            });
      },
      changeChapterNo: function() {
        var val = parseInt(this.chap_count.get('value'));
        if (isNaN(val) || val < 1 || val > this.sectionNames.length || (config.subChapEnable && val > config.subChaptersCount)) {
            alert(U.str.wrongnumber);
            this.chap_count.set('value', config.chapters.length);
            return ;
        }
        if (!confirm(U.str.warningchapnochange)) {
            this.chap_count.set('value', config.chapters.length);
            return false;
        }
        this.defaultGrouping(true).draw();
      },
      enableEdits: function () {
        var self = this;
            //"live" event listeners for chapter editing names
            this.table.delegate('click', function() {
                if (self.isEditingName) {
                    return false;
                }
                var tr = this.ancestor('tr'),
                    isChapter = (this.hasClass('cm-edit-chapter')),
                    span = tr.one(isChapter ? '.cm-chapter-name' : ''),// .cm-subchapter-name
                    input = tr.one(isChapter ? '.edit-chapter-name' : ''); //.edit-subchapter-name
                span.hide();
                input.show();
                input.focus();
                self.isEditingName = true;
            }, 'a.cm-edit-chapter, '); // a.cm-edit-subchapter
            this.table.delegate('keypress', function( e ) {
                var tr = this.ancestor('tr'),
                    isChapter = (this.hasClass('edit-chapter-name')),
                    span = tr.one(isChapter ? '.cm-chapter-name' : ''); //.cm-subchapter-name
                if (e.keyCode === 13) {
                    self.doneEditingName( span, this, isChapter );
                    e.preventDefault();
                    return false;
                }
            }, '.edit-chapter-name, '); // .edit-subchapter-name
            this.table.delegate('blur', function() {
                var tr = this.ancestor('tr'),
                    isChapter = (this.hasClass('edit-chapter-name')),
                    span = tr.one(isChapter ? '.cm-chapter-name' : ''); //.cm-subchapter-name
                    self.doneEditingName( span, this, isChapter );
            }, '.edit-chapter-name, '); //.edit-subchapter-name
            this.table.delegate('click', function() {
                var tr = this.ancestor('tr'),
                    dir = this.get('rel').split('-');
                self.moveTopic(tr, dir[0], typeof dir[1] !== 'undefined' ? dir[1] : null);
            }, '.cm-move-topic');
           /*  this.table.delegate('click', function() {
                var tr = this.ancestor('tr'),
                    dir = this.get('rel');
                self.moveSubChapter(tr, dir);
            }, '.cm-move-subchapter'); */
      },
      draw: function () {
          this.table.empty();
          this.table.append(this.getHeader()); 
          this.sectionCounter = 0;
            
          for (var i = 0; i < config.chapters.length; i++) {
              var chapter = config.chapters[i];
              this.table.append(this.getChapterRow(chapter, i));
                
              this.drawChapter(chapter, i);
          }
      },
      getHeader: function() {
        var html = '<tr><td align="center" colspan="2" width="400">' + U.str.chapters + '</td>';
       /*  if (config.subChapEnable) {
            html += '<td align="center" colspan="3" width="200">' + U.str.subchapters + '</td>';
        } */
        html += '<td align="center" colspan="2" width="400">' + U.str.sections + '</td></tr>'
        return html;
      },
      getChapterRow: function(chapter, index) {
        var chapterCounts = '';
        if (! config.chapEnable) { // || ! config.subChapEnable
            chapterCounts = chapter.childElements[0].count;
        }
        var html = '<tr id="cm-chapter-' + index + '"><td width="20" align="left" style="background-color:' + this.chapterBg + '">' + 
                '<a href="javascript:void(0)" class="cm-edit-chapter"><img alt="" src="' + U.img.edit + '" /></a></td>';
        //chapter cell
        html += '<td align="left" style="background-color:' + this.chapterBg + '"><span class="cm-chapter-name">' + chapter.name + '</span>' + 
                '<input type="text" style="display: none" class="edit-chapter-name" name="chapterNames[]" value="' + chapter.name + '" />' + 
                '<input type="hidden" name="chapterCounts[]" value="' + chapterCounts + '" />' + 
                '<input type="hidden" name="chapterChildElementsNumber[]" value="' + chapter.childElements.length + '" /></td>';
       /*  if (config.subChapEnable) {
            //3 empty <td>s
            html += '<td style="background-color:' + this.chapterBg + '">&nbsp;</td><td style="background-color:' + this.chapterBg + '">&nbsp;</td>';
            html += '<td style="background-color:' + this.chapterBg + '">&nbsp;</td>';
        } */
        //2 empty <td>s
        html += '<td style="background-color:' + this.chapterBg + '">&nbsp;</td><td style="background-color:' + this.chapterBg + '">&nbsp;</td>';
        return html;
      },
      drawChapter: function(chapter, i) {
        var clr, html = '', next = {}, previous = {}, 
                    count = chapter.childElements.length, 
                    chapterCount = config.chapters.length;
            
            for (var k = 0; k < count; k++) {
                var element = chapter.childElements[k];
                if (config.subChapEnable) {
                    if (element.type === 'topic') {
                        clr = this.topicBg;
                    } else {
                        clr = this.subChapterBg;
                    }
                    //2 empty <td>s
                    html += '<tr id="cm-subchapter-' + i + '-' + k + '"><td>&nbsp;</td><td>&nbsp;</td>';
                    var w = '20', temp = '';
                    // add move image
                    if (element.type === 'topic' && (next.type === 'subchapter' || previous.type === 'subchapter')) {
                        w = '40';
                    }
                    
                    if (i > 0 && k === 0 && count > 1) {
                        w = '40';
                        //move up subchapter
                        temp = '<a href="javascript:void(0)" class="cm-move-subchapter" rel="up"><img alt="" src="' + U.img.up + '" /></a>';
                    } else if (i !== chapterCount - 1 && k === count - 1 && count > 1) {
                        w = '40'
                        //move down subchapter
                        temp = '<a href="javascript:void(0)" class="cm-move-subchapter" rel="down"><img alt="" src="' + U.img.down + '" /></a>'
                    }
                    html += '<td align="center" style="background-color:' + clr + '" width="' + w + '">' + temp;
                    //gets the next (row) that could be: 1) next child element of current chapter; 2) new chapter - first child element; 3) empty for the last chapter, last row
                    next = (k === count - 1) ? (i === chapterCount - 1 ? {type:'___'} : config.chapters[i + 1].childElements[0]) : chapter.childElements[k + 1];
                    //get the previous (row): 1) last child of the previous chapter; 2) previous child element in current chapter; 3) empty for first chapter, first row
                    previous = (k === 0) ? (i === 0 ? {type:'___'} : config.chapters[i - 1].childElements.slice(-1)[0]) : chapter.childElements[k - 1];
                    
                    if (element.type === 'topic' && (next.type === 'subchapter' || previous.type === 'subchapter')) {
                        //move topic right (topic in column of subchapter)
                        var a = '<a href="javascript:void(0)" class="cm-move-topic"';
                        
                        if (previous.type === 'subchapter') {
                            a += ' rel="right-above"';
                        } else {
                            a += ' rel="right-below"';
                        }
                        a += '><img alt="" src="' + U.img.right + '" /></a>';
                        html += a;
                    }
                    html += '</td>';
                    // add edit subchapter name
                    html += '<td width="20" align="center" style="background-color:' + clr + '">';
                    if (element.type === 'subchapter') {
                        //edit link
                        html += '<a href="javascript:void(0)" class="cm-edit-subchapter"><img alt="" src="' + U.img.edit + '" /></a>';
                    } else {
                        //do nothing, this will be empty for now
                        html += '&nbsp;'
                    }
                    html += '</td>';

                    //add subchapter name column or topic name if type == "topic"
                    html += '<td align="left" style="background-color:' + clr + '">';
                    if (element.type === 'subchapter') {
                        html += '<span class="cm-subchapter-name">' + element.name + '</span>';
                    } else if (element.type === 'topic') {
                        html += '<span class="cm-section-name">' + this.sectionNames[this.sectionCounter++] + '</span>';
                    }
                    //create inputs
                    html += '<input type="text" style="display: none" class="edit-subchapter-name" name="childElementNames[]" value="';
                    html += (element.type === 'subchapter' ? element.name : '') + '" />';
                    html += '<input type="hidden" name="childElementCounts[]" value="';
                    html += (element.type === 'subchapter' ? element.count : '') + '" />';
                    html += '<input type="hidden" name="childElementTypes[]" value="' + element.type + '" />';

                    // add 2 empty <td>s
                    html += '</td><td style="background-color:' + clr + '">&nbsp;</td><td style="background-color:' + clr + '">&nbsp;</td>';
                    //end row
                    html += '</tr>';
                }
                if (! config.subChapEnable || element.type === 'subchapter') { //create topics
                    for (var j = 0; j < element.count; j++) {
                        //2 empty <td>s
                        html += '<tr id="index-' + i + '-' + k + '-' + j + '"><td>&nbsp;</td><td>&nbsp;</td>';
                        
                        //add another 3 empty tds if subchaptersEnable
                        if (config.subChapEnable) {
                            html += '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
                        }
                        // add move image
                        html += '<td align="center" style="background-color:' + this.topicBg + '"';
                        var tw = '20';
                        
                        if (element.count > 1 && j === 0 && (i > 0 || (k > 0 && config.subChapEnable))) {
                            var links = '';
                            //move the topic left and above the subchapter
                            if (config.subChapEnable) {
                                tw = '40';
                                links += '<a href="javascript:void(0)" class="cm-move-topic" rel="left-above"><img alt="" src="' + U.img.left + '" /></a>'
                            }
                            //move up link
                            links += '<a href="javascript:void(0)" class="cm-move-topic" rel="up"><img alt="" src="' + U.img.up + '" /></a>';
                            html += ' width="' + tw + '">' + links;
                            
                        } else if (element.count > 1 && j === element.count - 1 && (i < chapterCount - 1 || (k < count - 1 && config.subChapEnable))) {
                            var links = '';
                            //move topic left and below current subchapter
                            if (config.subChapEnable) {
                                tw = '40';
                                links += '<a href="javascript:void(0)" class="cm-move-topic" rel="left-below"><img alt="" src="' + U.img.left + '" /></a>'
                            }
                            //move down link
                            links += '<a href="javascript:void(0)" class="cm-move-topic" rel="down"><img alt="" src="' + U.img.down + '" /></a>';
                            html += ' width="' + tw + '">' + links;
                        }
                        html += '</td>';
                        // add section name
                        html += '<td align="left" style="background-color:' + this.topicBg + '">' + this.sectionNames[this.sectionCounter++] + '</td></tr>';
                    }
                }
            }
            this.table.append(html);
      },
      doneEditingName: function(span, input, isChapter) {
        var tr = span.ancestor('tr'),
            name = input.get('value');
        if (! name) {
            alert( isChapter ? U.str.emptychapname :  'Enter a Chapter name'); // U.str.emptysubchapname
            return true;
        }
        input.hide();
        span.show().set('innerHTML', name);
        this.isEditingName = false;
        if (isChapter) {
            var index = parseInt(tr.get('id').replace('cm-chapter-', ''));
            config.chapters[index].name = name;
        } /* else {
            var parts = tr.get('id').replace('cm-subchapter-', '').split('-');
            config.chapters[parseInt(parts[0])].childElements[parseInt(parts[1])].name = name;
        } */
      },
      defaultGrouping: function(hardReset) {
            
        if (hardReset) {
            config.chapters = [];
        } else {
            this.chap_count.set('value', config.chapters.length);
        }
        var chapNo = parseInt(this.chap_count.get('value'));
        var c = Math.floor(this.sectionNames.length / chapNo);
        var r = this.sectionNames.length - c * chapNo;
        for (var i = 0; i < chapNo; i++) {
            if (! config.subChapEnable || hardReset) {
                if (hardReset) {
                    config.chapters[i] = {
                        name: U.str.chapter + ' ' + (i+1)
                    };
                }
                config.chapters[i].childElements = [];
                config.chapters[i].childElements[0] = {
                    type: 'subchapter',
                    count: i < r ? c + 1 : c
                };
            }
        }
        this.resetSubchapterGroupings(hardReset);
        return this;
      },
      defaultGrouping: function(hardReset) {
            
        if (hardReset) {
            config.chapters = [];
        } else {
            this.chap_count.set('value', config.chapters.length);
        }
        var chapNo = parseInt(this.chap_count.get('value'));
        var c = Math.floor(this.sectionNames.length / chapNo);
        var r = this.sectionNames.length - c * chapNo;
        for (var i = 0; i < chapNo; i++) {
            console.log('That is Chapter number', chapNo);
            //if (! config.subChapEnable || hardReset) { 
                if (hardReset) {
                    config.chapters[i] = {
                        name: U.str.chapter + ' ' + (i+1)
                    };
                }
                config.chapters[i].childElements = [];
               /*  config.chapters[i].childElements[0] = {
                    type: 'subchapter',
                    count: i < r ? c + 1 : c
                }; */
          //}
        }
        //this.resetSubchapterGroupings(hardReset);
        return this;
      },
      moveTopic: function(){
        var target = tr.get('id').replace('cm-subchapter-', '').replace('index-', '').replace('cm-chapter-', '').split('-'),
        upperTR = tr.previous(), //this should be TR with subchapter
        mostUpperTR	= upperTR.previous(), //chapter or normal topic from another subchapter
        evenMoreUpperTR = mostUpperTR.previous(),
        lowerTR = tr.next(),
        mostLowerTR = lowerTR.next(),
        chapterIndex = parseInt(target[0]),
        subChapterIndex = parseInt(target[1]);
        var targetChapterIndex = chapterIndex + 1;
        if (config.subChapEnable) {
          if (direction === 'up') {
              if (mostUpperTR.all('td').item(4).one('.cm-section-name') || evenMoreUpperTR.all('td').item(4).one('.cm-section-name')) {
                  alert(U.str.cannotmovetopicup);
                  return;
              }
              if (subChapterIndex === 0) {
                  var prev = config.chapters[chapterIndex - 1].childElements;
                  config.chapters[chapterIndex - 1].childElements[prev.length - 1].count++;
                  config.chapters[chapterIndex].childElements[subChapterIndex].count--;
              } else {
                  config.chapters[chapterIndex].childElements[subChapterIndex - 1].count++;
                  config.chapters[chapterIndex].childElements[subChapterIndex].count--;
              }
          } else if (direction === 'down') {
              if (lowerTR.all('td').item(4).one('.cm-section-name') || mostLowerTR.all('td').item(4).one('.cm-section-name')) {
                  alert(U.str.cannotmovetopicdown);
                  return;    
              }
              if (subChapterIndex === config.chapters[chapterIndex].childElements.length - 1) {
                  config.chapters[chapterIndex + 1].childElements[0].count++;
                  config.chapters[chapterIndex].childElements[subChapterIndex].count--;
              } else {
                  config.chapters[chapterIndex].childElements[subChapterIndex + 1].count++;
                  config.chapters[chapterIndex].childElements[subChapterIndex].count--;
              }
          } else if (direction === 'right') {
              config.chapters[chapterIndex].childElements.splice(subChapterIndex, 1);
              if (whereToInsert === 'above') {
                  if (subChapterIndex === 0) {
                      chapterIndex --;
                      subChapterIndex = config.chapters[chapterIndex].childElements.length;
                  }
                  config.chapters[chapterIndex].childElements[subChapterIndex - 1].count++;
              } else {
                  if (subChapterIndex === config.chapters[chapterIndex].childElements.length) {
                      chapterIndex++;
                      subChapterIndex = 0;
                  }
                  config.chapters[chapterIndex].childElements[subChapterIndex].count++;
              }
          } else if (direction === 'left') {
              var child = {
                  type: 'topic'
              };
              if (whereToInsert === 'above') {
                  config.chapters[chapterIndex].childElements.splice(subChapterIndex, 0, child);
                  config.chapters[chapterIndex].childElements[subChapterIndex + 1].count--;
              } else {
                  config.chapters[chapterIndex].childElements.splice(subChapterIndex + 1, 0, child);
                  config.chapters[chapterIndex].childElements[subChapterIndex].count --;
              }
          }
        } else {
          var targetChapterIndex = chapterIndex + 1;
          if (direction === 'up') {
              targetChapterIndex = chapterIndex - 1;
          }
          config.chapters[chapterIndex].childElements[0].count--;
          config.chapters[targetChapterIndex].childElements[0].count++;
        }
      this.draw();
        
      },
    };
    console.log('This is Chapter', M.format_mooin4);
    console.log('Href', window.location.href);
};

M.format_mooin4.hide = function() {
    for (var i = 1; i <= this.numsections; i++) {
        if (document.getElementById('mooin4ection-' + i) != undefined) {
            var mooin4ection = document.getElementById('mooin4ection-' + i);
            mooin4ection.setAttribute('class', mooin4ection.getAttribute('class').replace('sectionvisible', ''));
            // document.getElementById('section-' + i).style.display = 'none';
        }
    }
};

M.format_mooin4.show = function(id, courseid) {
    this.hide();
    if (id > 0) {
        document.location.hash = '#section=' + id ;
        var mooin4ection = document.getElementById('mooin4ection-' + id);
        var currentsection = document.getElementById('section-' + id);
        // var sectionvisible = document.getElementsByClassName('sectionvisble');
        var parentDOM = document.getElementById("slider_inner");
        console.log('Mooin4section', mooin4ection);
        console.log('currentsection', currentsection);
        if (mooin4ection && currentsection) {
            mooin4ection.setAttribute('class', mooin4ection.getAttribute('class') + ' sectionvisible');
            currentsection.style.display = 'block';
            document.cookie = 'sectionvisible_' + courseid + '=' + id + '; path=/';
            
            M.format_mooin4.h5p();
        }
        var testTarget =  parentDOM.getElementsByClassName("sectionvisible")[0]; // [0] 
        console.log('Show sectionvisible', testTarget);
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
