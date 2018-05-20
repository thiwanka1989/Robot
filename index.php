
<script>

//assigning robot cordinations
    var model = {
    currentRobot: null,
    init: {
        x: 0,
        y: 0,
        f: "north"
    }
};



var controller = {
    init: function () {
		
		//initiating robot
        model.currentRobot = model.init;
		
		
		//initiating command accepting 
        inputView.init();

		
		//initiating robot coedination repoting function
        reportView.init();

		
		//initiating table 
        canvasView.init();
    },
	
	//retriving robot current position
    getCurrentRobot: function () {
        return model.currentRobot;
    },
	
	//seting robot position
    setCurrentRobot: function (robot) {
        model.currentRobot = robot;
    },
	
	//resting to default
    resetContents: function () {
        reportView.clear();
    },
    
	
	//placing function start
    place: function (cmd) {
        var newPos = cmd.split(","); // get x y f from the command from one command
        if (newPos.length < 3) {
            this.printErrors("incorrect position / direction");
        } else {
            var newX = parseInt(newPos[0].trim()),
                newY = parseInt(newPos[1].trim()),
                newF = newPos[2].trim().toLowerCase();// converting user input (facing) to lowercase befor passing to validate

            if (canvasView.validateBound(newX, "maxX") && canvasView.validateBound(newY, "maxY") && canvasView.validateFacing(newF)) {// passing cordinations to validate

                this.clearOriginalRobot(); // clear the original robot first

                this.setCurrentRobot({
                    x: newX,
                    y: newY,
                    f: newF
                });
                canvasView.renderRobot();//seting current robot
            }
        }
    },
	//placing function end 
	
	//move function start 
    move: function () {
        var currentRobot = this.getCurrentRobot(),//get robot current position
            newRobot = {
                x: currentRobot.x,
                y: currentRobot.y,
                f: currentRobot.f
            };

			//moving robot according to the facing
        switch (currentRobot.f) {
            case "north":
                newY = currentRobot.y + 1;
                if (canvasView.validateBound(newY, "maxY")) {
                    this.clearOriginalRobot();

                    newRobot.y = newY;
                    this.setCurrentRobot(newRobot);
                    canvasView.renderRobot();
                }
                break;
            case "south":
                newY = currentRobot.y - 1;
                if (canvasView.validateBound(newY, "maxY")) {
                    this.clearOriginalRobot();
                    newRobot.y = newY;
                    this.setCurrentRobot(newRobot);
                    canvasView.renderRobot();
                }
                break;
            case "east":
                newX = currentRobot.x + 1;
                if (canvasView.validateBound(newX, "maxX")) {
                    this.clearOriginalRobot();
                    newRobot.x = newX;
                    this.setCurrentRobot(newRobot);
                    canvasView.renderRobot();
                }
                break;
            case "west":
                newX = currentRobot.x - 1;
                if (canvasView.validateBound(newX, "maxX")) {
                    this.clearOriginalRobot();
                    newRobot.x = newX;
                    this.setCurrentRobot(newRobot);
                    canvasView.renderRobot();
                }
                break;
            default:
                break;
        }


    },
	//move function end
	
	
	
	//left function start
    left: function () {
        this.rotate(false); // get the next from this.robotFacing array in anti-clockwise direction
    },
	//left function end
	
	
	//right function start
    right: function () {
        this.rotate(true); // get the next from this.robotFacing array in clockwise direction
    },
	//right function end
	
	
	//report function start
    report: function () {
        reportView.renderReport();//displaying current robot position
    },
	//report function end
	
	
	//rotate function start
	//according to right/clockwise(true) or left/anticlockwise(false)
    rotate: function (clockwise) {
        this.clearOriginalRobot();

        var currentRobot = this.getCurrentRobot(),
            originalFacing = currentRobot.f,
            originalFacingIndex = canvasView.robotFacing.indexOf(originalFacing),
            newFacingIndex,
            totalFacing = canvasView.robotFacing.length,
            newRobot = {
                x: currentRobot.x,
                y: currentRobot.y,
                f: currentRobot.f
            };

        if (clockwise) {//if user enterd right as command
            if (originalFacingIndex === (totalFacing - 1)) {
                newFacingIndex = 0;
            } else {
                newFacingIndex = originalFacingIndex + 1;
            }
        } else {// if user enterd left as command
            if (originalFacingIndex === 0) {
                newFacingIndex = totalFacing - 1;
            } else {
                newFacingIndex = originalFacingIndex - 1;
            }
        }

        newRobot.f = canvasView.robotFacing[newFacingIndex];
        this.setCurrentRobot(newRobot);
        canvasView.renderRobot();
    },
	//rotate function end
	
	//clearing current robot position function start
    clearOriginalRobot: function () {
        var origRobot = this.getCurrentRobot();
        canvasView.clearOriginalRobot(origRobot.x, origRobot.y); // clear the original robot first
    },
	//clearing current robot position function end
	
	
	//printing errors function start
    printErrors: function (msg) {
        reportView.renderErrors(msg);
    },
	//printing errors function end
	
	
	
	//reset function start
    reset: function () {
        this.clearOriginalRobot();
        this.setCurrentRobot(model.init);
        canvasView.renderRobot();
        inputView.reset();
    }
};
//reset function end


//user input accepting function start
var inputView = {
	
	//assigning valid commands 
    init: function () {
        this.commandBox = document.getElementById('command');

        this.allowedInput = ["place", "move", "left", "right", "report"];

        this.render();
    },
	
	// forcousing on user click 
    render: function () {
        this.commandBox.addEventListener('click', function () {
            this.select();
        });
    },
	
	//clearing command inpit area
    reset: function () {
        this.commandBox.innerHTML = '';
    },
	
	//executing the command entered by user 
    processCommand: function (value) {
        this.commandBox.select(); // auto select all input for easier editing

        controller.resetContents(); // remove previous status and errors

        var self = this,
            sanitizedValue = value.trim().toLocaleLowerCase(),
            sanitizedValueArray = sanitizedValue.split(' '),
            firstWordEntered = sanitizedValueArray.splice(0, 1)[0];

        if (self.allowedInput.indexOf(firstWordEntered) > -1) {
            controller[firstWordEntered](sanitizedValueArray.join()); // call controller functions by name
        } else {
            controller.printErrors("Incorrect command");
        }
    }
};


//defining the tabe 
var canvasView = {
    init: function () {
        this.maxX = 5; // x total
        this.maxY = 5; // y total
        this.squareSize = 100; // all grids are equal width and height
        this.xStart = 50; // axis x starts from 50px
        this.yStart = 50; // axis y starts from 50px
        this.xEnd = this.xStart + this.squareSize * this.maxX; // axis x starts from 50px
        this.yEnd = this.yStart + this.squareSize * this.maxY; // axis y starts from 50px
        this.canvas = document.getElementById("c");
        this.context = this.canvas.getContext("2d");
        this.renderCanvas();

        this.robotFacing = ['north', 'east', 'south', 'west']; // clockwise
        this.robotSize = 30; // is the arrow size actually
		model.currentRobot = model.init;
        this.renderRobot();

        this.resetButton = document.getElementById('restart');
        this.renderControls();
    },
	
	//when user click reset reseting controller
    renderControls: function () {
        var self = this;

        this.resetButton.addEventListener('click', function () {
            controller.reset();
        });
    },
	
	//creating lines
    renderCanvas: function () {
        for (var x = 0; x < (this.maxX + 1); x++) { // draw 6 lines
            var currentAxisX = this.xStart + x * this.squareSize;
            this.context.moveTo(currentAxisX, this.yStart);
            this.context.lineTo(currentAxisX, this.yEnd);

            this.context.strokeText(x, currentAxisX + 50, this.yEnd + 20); // mark x index
        }

        for (var y = 0; y < (this.maxY + 1); y++) {
            var currentAxisY = this.yStart + y * this.squareSize;
            this.context.moveTo(this.xStart, currentAxisY);
            this.context.lineTo(this.xEnd, currentAxisY);

            this.context.strokeText((this.maxY - 1 - y), this.xStart - 20, currentAxisY + 50); // mark y index
        }

        this.context.strokeStyle = "#000";
        this.context.stroke();
    },
	
	//validating input as axies and is those incide the table
    validateBound: function (input, toCheckAxis) {
        if (isNaN(input)) {
            controller.printErrors("Please enter a numeric coordinates!");
            return false;
        } else if (input < 0 || input > (this[toCheckAxis] - 1)) {
            controller.printErrors("Coordinates out of range!");
            return false;
        } else {
            return true;
        }
    },
	
	//validating robot direction
    validateFacing: function (face) {
        if (this.robotFacing.indexOf(face.toLowerCase()) < 0) {
            controller.printErrors("Wrong facing!");
            return false;
        } else {
            return true;
        }
    },
	
	
	//according to the command displaying robot(move)
    clearOriginalRobot: function (origX, origY) {
        var axisX = origX * 100 + 51; // left most point of the current grid deduct stroke width
        var axisY = (this.maxY - origY) * 100 - 49; // top most point of the current grid deduct stroke width
        this.context.clearRect(axisX, axisY, 98, 98);
    },
	
	//according to the command displaying robot(facing)
    renderRobot: function () {
        var robot = controller.getCurrentRobot(),
            robotAxisX = (robot.x + 1) * 100, // the center of the destination grid horizontally
            robotAxisY = (this.maxY - robot.y) * 100; // the center of the destination grid vertically

        var path = new Path2D();
        this.context.beginPath();
        switch (robot.f) {
            case "north":
                path.moveTo(robotAxisX, robotAxisY - this.robotSize);
                path.lineTo(robotAxisX - this.robotSize, robotAxisY);
                path.lineTo(robotAxisX + this.robotSize, robotAxisY);
                break;
            case "south":
                path.moveTo(robotAxisX, robotAxisY + this.robotSize);
                path.lineTo(robotAxisX - this.robotSize, robotAxisY);
                path.lineTo(robotAxisX + this.robotSize, robotAxisY);
                break;
            case "east":
                path.moveTo(robotAxisX + this.robotSize, robotAxisY);
                path.lineTo(robotAxisX, robotAxisY - this.robotSize);
                path.lineTo(robotAxisX, robotAxisY + this.robotSize);
                break;
            case "west":
                path.moveTo(robotAxisX - this.robotSize, robotAxisY);
                path.lineTo(robotAxisX, robotAxisY - this.robotSize);
                path.lineTo(robotAxisX, robotAxisY + this.robotSize);
                break;
            default:
                break;
        }

        this.context.closePath();
        this.context.fill(path);

        controller.report();
    }
};


//indicating robrt positions
var reportView = {
    init: function () {
        this.reportMessageEle = document.getElementById("report");
    },
    renderReport: function () {
        var currentRobot = controller.getCurrentRobot();
        this.reportMessageEle.innerHTML = '<span>' + 'Axis X: ' + currentRobot.x + '</span></br>' +
            '<span>' + ' Axis Y: ' + currentRobot.y + '</span></br>' +
            '<span>' + ' Facing: ' + currentRobot.f + '</span></br>';
    },
    renderErrors: function (msg) {
        this.reportMessageEle.innerHTML = '<span id="error">' + msg + '</span>';
    },
    clear: function () {
        this.reportMessageEle.innerHTML = '';
     }
};

//when loading the page initiating controller function
 window.onload = function () {
     controller.init();
 };

//if needed place only by user (no default)
//<p id="place" onclick="myFunction()">PLACE Robot</p> paste this before reset button
function myFunction() {
    controller.init();
}

//using file inuting commands
var openFile = function(event) {
        var input = event.target;

        var reader = new FileReader();
        reader.onload = function(){
          var text = reader.result;
          var node = document.getElementById('output');
          node.innerText = text;
		  document.getElementById('command').value=text;
		  
          console.log(reader.result.substring(0, 200));
        };
        reader.readAsText(input.files[0]);
      };
</script>



<p>The application can read commands in following format and only one command at a time (case insensitive):
    <ul>
        <li>PLACE X,Y,F</li>
        <li>MOVE</li>
        <li>LEFT</li>
        <li>RIGHT</li>
        <li>REPORT</li>
    </ul>
</p>
<main>
    <div class="input-area">
        <label for="command">Enter Command:</label>
        <input type="text" id="command" onfocusout="inputView.processCommand(this.value)" onkeydown="if (event.keyCode == 13) inputView.processCommand(this.value);" autofocus>
    </div>
    <div class="content">
         <h4>Current Status:</h4>

        <p id="report"></p>
		
        <button id="restart">Restart</button></br>
		</br><input type='file' accept='text/plain' onchange='openFile(event)'></br>
    </div>
    <canvas id="c" width="551" height="580"></canvas>
	
    <div id='output'>
    ...
    </div>
	
	
</main>

