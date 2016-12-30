;(function($) {

    var Paint = function($container, $saveButton, image, urls) {

        this.container = $container;
        this.canvas = this.container.find('canvas');
        this.saveButton = $saveButton;
        this.image = image;

        if (urls != undefined) {
            this.addUrl = urls.add;
            this.updateUrl = urls.update;
            this.detailUrl = urls.detail;
        }

        this.drawState = false;
        this.offset = this.canvas.offset();

        this.lastPosition = this.currentPosition = {
            x: 0,
            y: 0,
        };

        var self = this;

        this.run = function (width, color) {

            this.canvas.css('background-color', 'white');

            if (this.image != undefined && this.image != false) {
                this.canvas.drawImage({
                    source: self.image.path,
                    x: 0,
                    y: 0,
                    width: self.canvas.width(),
                    height: self.canvas.height(),
                    fromCenter: false
                });
            }

            //save init
            this.saveButton.on('click', function(e) {
                e.preventDefault();
                self.save();
            })

            //draw init
            $('canvas').on('mousedown', function(e) {
                e.preventDefault();
                self.drawState = true;
            });

            $('canvas').on('mouseup', function(e) {
                self.drawState = false;
            });

            $('canvas').on('mousemove', function(e) {

                self.lastPosition = self.currentPosition;

                self.currentPosition = {
                    x: e.pageX - self.offset.left,
                    y: e.pageY - self.offset.top
                };

                if (self.draw) {
                    self.draw(self.lastPosition, self.currentPosition, color, width);
                }
            });
        };

        this.draw = function(lastPosition, currentPosition, color, width) {

            if (color == undefined) {
                color = '#000';
            }

            if (width == undefined) {
                width = 10;
            }

            if (self.drawState) {
                $('canvas').drawLine({
                    strokeStyle: color,
                    strokeWidth: width,
                    rounded: true,
                    strokeJoin: 'round',
                    strokeCap: 'round',
                    x1: lastPosition.x,
                    y1: lastPosition.y,
                    x2: currentPosition.x,
                    y2: currentPosition.y
                });
            }
        };

        this.save = function() {

            var data = {},
                url;
            
            var $pass = this.container.find('input[name=pass]');
            
            if ($pass && $pass.length > 0) {
                
                $pass.parent().removeClass('has-error');
                var pass = $pass.val();
                
                if (pass == '') {
                    $pass.parent().addClass('has-error');
                    return;
                }
                
                data.pass = pass;
            }

            var $token = this.container.find('input[name=token]');
            if ($token) {
                var token = $token.val();
                data.token = token;
            }

            var imageData = self.canvas.getCanvasImage('png');
            data.image = imageData;

            if (self.image != false) {
                url = self.updateUrl;
            } else {
                url = self.addUrl;
            }

            $.ajax({
                url: url,
                method: "POST",
                data: data,
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                dataType: 'json',
                success: function(data, textStatus, jqXHR) {

                    if (data['success']) {

                        if (data['message']) {
                            alert(data['message']);
                        }

                        if (data['location']) {
                            window.location = data['location'];
                        }
                    } else if (data['error']) {
                        alert(data['error']);
                    }
                },
                fail: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR, textStatus, errorThrown);
                }
            });
            
        };
    };

    window.Paint = Paint;
})(jQuery);