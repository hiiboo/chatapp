<?php
    session_start();
    include_once "config.php";
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if(!empty($fname) && !empty($lname) && !empty($email) && !empty($password)){
        //let's check user email is vaild or not
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){ //if email is vailed
            // let's check that email already exist in the database or not
            $sql = mysqli_query($conn, "SELECT email FROM users WHERE email = '{$email}'");
            if(mysqli_num_rows($sql) > 0){ //if email already exist
                echo "$email - This email alreay exist!";
            }else{
                //let's check user upload file or not
                if(isset($_FILES['image'])){ // if file is uploaded
                    $img_name = $_FILES['image']['name']; //getting user uploaded img name
                    $img_type = $_FILES['image']['type'];
                    $tmp_name = $_FILES['image']['tmp_name']; //this temporary name is used to save/move file in our folder

                    //let's explode image and get the last extension like jpg png
                    $img_explode = explode('.', $img_name);
                    $img_ext = end($img_explode); //here I get the extension of an user upload img file

                    $extensions = ['png', 'jpeg', 'jpg']; //these are some vaild img ext and I've store them in array
                    if(in_array($img_ext, $extensions) === true){
                        $types = ["image/jpeg", "image/jpg", "image/png"];
                        if(in_array($img_type, $types) === true){
                        $time = time(); //this will return us current time..
                        //let's move the user uploaded img to our particular folder
                        $new_img_name = $time.$img_name;

                        if(move_uploaded_file($tmp_name, "images/".$new_img_name)){ //if user upload img move to my folder succesfully
                            $ran_id = rand(time(), 10000000); //creating random id for user
                            $status = "Active now"; //once user signed up then his status will be active now
                            $encrypt_pass = md5($password); //let's insert all user data inside table
                            $insert_query = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, password, img, status)
                                VALUES ({$ran_id}, '{$fname}','{$lname}', '{$email}', '{$encrypt_pass}', '{$new_img_name}', '{$status}')");
                                if($insert_query){
                                    $select_sql2 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
                                    if(mysqli_num_rows($select_sql2) > 0){
                                        $result = mysqli_fetch_assoc($select_sql2);
                                        $_SESSION['unique_id'] = $result['unique_id'];
                                        echo "success";
                                    }else{
                                        echo "This email address not Exist!";
                                    }
                                }else{
                                    echo "Something went wrong. Please try again!";
                                }
                            }
                        }else{
                            echo "Please upload an image file - jpeg, png, jpg";
                        }
                    }else{
                        echo "Please upload an image file - jpeg, png, jpg";
                    }
                }
            }
        }else{
            echo "$email is not a valid email!";
        }
    }else{
        echo "All input fields are required!";
    }
?>