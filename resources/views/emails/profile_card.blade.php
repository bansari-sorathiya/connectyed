<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .profile-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            max-width: 550px;
            margin: 20px auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }
        .profile-card h2 {
            text-align: center;
            color: #333;
        }
        .profile-card p {
            margin: 5px 0;
            color: #555;
        }
        .table th{
            width:150px;
        }
    </style>
</head>
<body>
    
<center><h4 clas="mt-5"> <strong> {{$client->name}} </strong> has requested for Free 1 on 1 Blind Date.</h4></center>
<div class="profile-card">
    <div class="table-responsive">
        <table class="table table-light">
            
            <tbody>
                <tr>
                    <th scope="col">Name</th>
                    <td scope="col"> {{$client->name}}</td>
                </tr>
                <tr>
                    <th scope="col">Username</th>
                    <td scope="col"> {{$client->username}}</td>
                </tr>
                <tr>
                    <th scope="col">Email</th>
                    <td> {{$client->email}}</td>
                </tr>
                <tr>
                    <th scope="col">Gender</th>
                    <td> {{$client->profile->gender}}</td>
                </tr>
                <tr>
                    <th scope="col">Age</th>
                    <td> {{$client->profile->age}}</td>
                </tr>
                <tr>
                    <th scope="col">City</th>
                    <td> {{$client->profile->city}}</td>
                </tr>
                <tr>
                    <th scope="col">State</th>
                    <td> {{$client->profile->state}}</td>
                </tr>
                <tr>
                    <th scope="col">Country</th>
                    <td> {{$client->profile->country}}</td>
                </tr>
                <tr>
                    <th scope="col">Body Type</th>
                    <td>{{$client->profile->bodytype}}</td>
                </tr>
                <tr>
                    <th scope="col">Height</th>
                    <td>{{$client->profile->height}}</td>
                </tr>
                <tr>
                    <th scope="col">Hair Color</th>
                    <td>{{$client->profile->haircolor}}</td>
                </tr>
                <tr>
                    <th scope="col">Marital Status</th>
                    <td>{{$client->profile->maritalstatus}}</td>
                </tr>
                <tr>
                    <th scope="col">Children</th>
                    <td>{{$client->profile->children}}</td>
                </tr>
                <tr>
                    <th scope="col">Religion</th>
                    <td>{{$client->profile->religion}}</td>
                </tr>
                <tr>
                    <th scope="col">Smoker</th>
                    @if ($client->profile->smoker == '0')
                        <td>Yes</td>
                    @else
                        <td>No</td>
                    @endif
                </tr>
                <tr>
                    <th scope="col">Drinker</th>
                    <td>{{$client->profile->drinker}}</td>
                </tr>
                <tr>
                    <th scope="col">Education</th>
                    <td>{{$client->profile->education}}</td>
                </tr>
                <tr>
                    <th scope="col">Job Title</th>
                    <td>{{$client->profile->jobtitle}}</td>
                </tr>
                <tr>
                    <th scope="col">Sports</th>
                    <td>{{$client->profile->sports}}</td>
                </tr>
                <tr>
                    <th scope="col">Hobbies</th>
                    <td>{{$client->profile->hobbies}}</td>
                </tr>
                <tr>
                    <th scope="col">Languages</th>
                    <td>{{$client->profile->languages}}</td>
                </tr>
                <tr>
                    <th scope="col">English level</th>
                    <td>{{$client->profile->english_level}}</td>
                </tr>
                <tr>
                    <th scope="col">Seeking</th>
                    <td>{{$client->profile->seeking}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
</div>

</body>
</html>