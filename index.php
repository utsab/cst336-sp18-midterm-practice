<?php
session_start(); 

/*

Plan: 
- Create a form that takes in all the appropriate information, output all the values DONE
- take all the values and create the appropriate grid of balls DONE
- start with the basics, create the grid with appropriate dimensions DONE
- Logic for computing winner  DONE
- Odd/even styles DONE
- Add special cases for ascending/descending
- Add in sessions, remember overall tally for who won (odd's vs. evens)
- Add validation + error messages
*/



if (!isset($_SESSION['winCounts'])) {
    $_SESSION['winCounts'] = array(
        'evens' => 0, 
        'odds' => 0
        ); 
}
    
$numRows = 3; 
$numCols = 3; 

if (isset($_GET['num-rows']) && !empty($_GET['num-rows'])) {
    $numRows = $_GET['num-rows']; 
} 


if (isset($_GET['num-cols']) && !empty($_GET['num-cols'])) {
    $numCols = $_GET['num-cols']; 
}

$include8Ball = isset($_GET['include-8-ball']) &&  $_GET['include-8-ball'] == "yes"; 

if (isset($_GET['order'])) {
    $order = $_GET['order']; 
}



function generatePossibleBalls($include8Ball, $numRows, $numCols) {
    $numBalls = $numCols * $numRows; 
    
    $balls = array(0, 1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14, 15); 
    shuffle($balls);
    
    $balls = array_slice($balls, 0, $numBalls); 
    
    if ($include8Ball) {
        array_shift($balls);
        array_push($balls, 8);
    }
    
    shuffle($balls);
    return $balls; 
}

$errors = validateInputs($numCols, $numRows); 

$balls = generatePossibleBalls($include8Ball, $numRows, $numCols); 


function displayErrors($errors) {
    foreach ($errors as $error) {
        echo "<div class='error'>$error</div>"; 
    }
}

function validateInputs($numRows, $numCols) {
    $errors = array(); 
    if ($numRows > 4 || $numCols > 4) {
        array_push($errors, "Dimensions cannot exceed 4"); 
    }
    
    return $errors; 
}


function createGrid($numRows, $numCols) {
    $evenScore = 0; 
    $oddScore = 0; 
    
    echo "<table>"; 
    
    for ($i = 0; $i < $numRows; $i++) {
        echo "<tr>"; 
        
        for ($j = 0; $j < $numCols; $j++) { 
            
            $ballIndex = getRandomBall(); 
            $ballClass = ""; 
            
            if ($ballIndex != 0) {
                if ($ballIndex % 2 == 0) {
                    $evenScore += $ballIndex; 
                    $ballClass = "even"; 
                } else {
                    $oddScore += $ballIndex; 
                    $ballClass = "odd"; 
                }
            }
            
            echo "<td class=$ballClass>";
            $imgURL = "./imgs/".$ballIndex.".png"; 
            echo "<img src='".$imgURL."'/>";  
            echo "</td>"; 
        }
        echo "</tr>"; 
    }
    
    echo "</table>";
    
    
    
    displayResultsOfRound($evenScore, $oddScore); 
    displayWinner($evenScore, $oddScore); 
    displayWinHistory(); 
}

function displayResultsOfRound($evenScore, $oddScore) {
    echo "<div class='results'>"; 
    echo "<strong>Results of this round</strong><br/>"; 
    echo "Even Balls: " . $evenScore . " points,  "; 
    echo "Odd Balls: " . $oddScore. " points "; 
    echo "</div>";
}

function displayWinner($evenScore, $oddScore) {
    if ($evenScore == $oddScore) {
        echo "<div> This round is a tie! </div>"; 
    } elseif ($evenScore > $oddScore) {
        "<div>Even balls win! </div>"; 
        $_SESSION['winCounts']['evens']++; 
        
    } else {
        "<div>Odd balls win!</div>"; 
        $_SESSION['winCounts']['odds']++; 
    }
}

function displayWinHistory() {
    echo "<div class='results'>"; 
    echo "<strong>Win History</strong><br/>"; 
    echo "Even Balls: " . $_SESSION['winCounts']['evens']. " wins,  "; 
    echo "Odd Balls: " . $_SESSION['winCounts']['odds']. " wins ";
    echo "<form class='win-history' action='clear-history.php'>";
    echo "<input type='submit' value='Clear history'/>";
    echo "</form>";
    echo "</div>"; 
}

function getRandomBall() {
    global $balls; 
    
    $ballIndex = array_shift($balls); 
    return $ballIndex; 
}

?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <form class='grid-info'>
            Rows: <input type="text" name="num-rows"></input>
            Columns: <input type="text" name="num-cols"></input>
            <br/>
            <input type="checkbox" name="include-8-ball" value="yes"> Include 8-ball <br/>
            <br/>
            <input type="submit" value="Display!" />
        </form>
        
        <?php
            if (count($errors) > 0) {
                displayErrors($errors); 
            } else {
                createGrid($numRows, $numCols); 
            }
        ?>
    </body>
</html>