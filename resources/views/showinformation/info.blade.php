@extends('layouts.main')


@section('container1')
    <h1>{{$message}}</h1>
@endsection

@section('container2')
    <style>
        .inbox-container {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }

        .inbox-details {
            flex-grow: 1;
        }

        .inbox-title {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
            text-decoration: none;
            transition: color 0.3s;
        }

        .inbox-title:hover {
            color: #007bff;
        }

        .indicators {
            display: flex;
            align-items: center;
        }

        .indicator {
            width: 12px;
            height: 12px;
            display: inline-block;
            border-radius: 50%;
            margin-right: 8px;
            cursor: pointer;
        }

        .red {
            background-color: red;
        }

        .green {
            background-color: green;
        }

        .indicator-tooltip {
            display: none;
            position: absolute;
            background-color: #333;
            color: white;
            padding: 5px;
            border-radius: 3px;
            z-index: 1;
        }

        .indicator:hover .indicator-tooltip {
            display: block;
        }

        .countup {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: bold;
            display: none; /* Initially hide the count-up */
        }

        .countup::before {
            content: "Count-up: ";
        }

        .status-badge {
            font-size: 0.9rem;
            margin-top: 5px;
        }
    </style>
@endsection
