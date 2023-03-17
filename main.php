<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles.css" />
</head>
<body>

    <h1>Filter reviews</h1>
    <form action="main.php" method="post">
        Order by rating:
        <br>
        <select name="HighLowFirst">  
            <option value="HighestFirst">Highest First</option>
            <option value="LowestFirst">Lowest First</option>   
        </select>
        <br>
        Minimum rating
        <br>
        <select name="MinRating">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select> 
        <br>
        Order by date:
        <br>
        <select name="DateOrder">    
            <option value="NewestFirst">Newest First</option>
            <option value="OldestFirst">Oldest First</option>   
        </select> 
        <br>
        Prioritize by text:
        <br>
        <select name="TextPrio"> 
            <option value="TextPrioYes">Yes</option>
            <option value="TextPrioNo">No</option>   
        </select>   
        <br>
        <input id="submitbtn" type="submit" name="submit" value="Submit" />
    </form> 


    <?php
    $string = file_get_contents('reviews.json');
    $reviews = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $string), true );
    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        echo 'Rating: ' . $_POST['HighLowFirst'];
        echo '<br>';
        echo 'Minimum rating: ' . $_POST['MinRating'];
        echo '<br>';
        echo 'Date order: ' . $_POST['DateOrder'];
        echo '<br>';
        echo 'Prioritize by text: ' . $_POST['TextPrio'];
        Filter($reviews);
        
    }
    function Filter($reviews){
        
        if($_POST['HighLowFirst']=='HighestFirst'){//ASCENDING RATING ORDER
            for($i=0; $i<count($reviews); $i++){
                for($j=0; $j<count($reviews)-1; $j++){
                    if($reviews[$j]['rating']<$reviews[$j+1]['rating']){
                        $temp=$reviews[$j];
                        $reviews[$j]=$reviews[$j+1];
                        $reviews[$j+1]=$temp;
                    }
                }
            }
        }else{                                    //DESCENDING RATING ORDER
            for($i=0; $i<count($reviews); $i++){
                for($j=0; $j<count($reviews)-1; $j++){
                    if($reviews[$j]['rating']>$reviews[$j+1]['rating']){
                        $temp=$reviews[$j];
                        $reviews[$j]=$reviews[$j+1];
                        $reviews[$j+1]=$temp;
                    }
                }
            }
        }

        if($_POST['DateOrder']=='NewestFirst'){ //NEWEST DATE FIRST
            for($i=0; $i<count($reviews); $i++){
                for($j=0; $j<count($reviews)-1; $j++){
                    if($reviews[$j]['reviewCreatedOnTime']<$reviews[$j+1]['reviewCreatedOnTime'] && $reviews[$j]['rating']==$reviews[$j+1]['rating']){
                        $temp=$reviews[$j];
                        $reviews[$j]=$reviews[$j+1];
                        $reviews[$j+1]=$temp;
                    }
                }
            }
        }else{                                  //OLDEST DATE FIRST
            for($i=0; $i<count($reviews); $i++){
                for($j=0; $j<count($reviews)-1; $j++){
                    if($reviews[$j]['reviewCreatedOnTime']>$reviews[$j+1]['reviewCreatedOnTime'] && $reviews[$j]['rating']==$reviews[$j+1]['rating']){
                        $temp=$reviews[$j];
                        $reviews[$j]=$reviews[$j+1];
                        $reviews[$j+1]=$temp;
                    }
                }
            }
        }


        //TABLE CREATION
        if($_POST['TextPrio']=='TextPrioYes'){ 
            $textArr=array();
            $noTextArr=array();
            for($i=0; $i<count($reviews); $i++){
                if($reviews[$i]['reviewText']==''){
                    $noTextArr[count($noTextArr)]=$reviews[$i];
                }else{
                    $textArr[count($textArr)]=$reviews[$i];
                }
            }
            echo '<br>With text:<br><table> <th>id</th> <th>rating</th> <th>date</th> </tr>';
            for($i=0; $i<count($textArr); $i++){
                echo '<tr>';
                if($textArr[$i]['rating']>=$_POST['MinRating']){//MINIMUM RATING CHECK
                    echo '<td>' . $textArr[$i]['id'] . '</td><td>' . $textArr[$i]['rating'] . '</td><td>' . $textArr[$i]['reviewCreatedOnDate'];
                }
                echo '</tr>';
            }
            echo '</table>';

            echo '<br>Without text:<br><table> <th>id</th> <th>rating</th> <th>date</th> </tr>';
            for($i=0; $i<count($noTextArr); $i++){
                echo '<tr>';
                if($noTextArr[$i]['rating']>=$_POST['MinRating']){//MINIMUM RATING CHECK
                    echo '<td>' . $noTextArr[$i]['id'] . '</td><td>' . $noTextArr[$i]['rating'] . '</td><td>' . $noTextArr[$i]['reviewCreatedOnDate'];
                }
                echo '</tr>';
            }
            echo '</table>';
        }else{
            echo '<table> <th>id</th> <th>rating</th> <th>date</th> </tr>';
            for($i=0; $i<count($reviews); $i++){
                echo '<tr>';
                if($reviews[$i]['rating']>=$_POST['MinRating']){//MINIMUM RATING CHECK
                    echo '<td>' . $reviews[$i]['id'] . '</td><td>' . $reviews[$i]['rating'] . '</td><td>' . $reviews[$i]['reviewCreatedOnDate'];
                }
                echo '</tr>';
            }
            echo '</table>';
        }


    }
    ?>
</body>
</html>
