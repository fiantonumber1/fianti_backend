<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horizontal Timeline with Branches</title>
    <style>
        // Define variables
        $content-width: calc(40vw - 84px);
        $margin: 20px;
        $spacing: 20px;
        $bdrs: 6px;
        $circle-size: 40px;
        $icon-size: 32px;
        $bdrs-icon: 100%;

        // Define color palette
        $color1: #9251ac;
        $color2: #f6a4ec;
        $color3: #87bbfe;
        $color4: #555ac0;
        $color5: #24b47e;
        $color6: #aff1b6;

        // Universal styling
        * {
            box-sizing: border-box;
        }

        html {
            font-size: 14px;
        }

        body {
            background: #f6f9fc;
            font-family: "Open Sans", sans-serif;
            color: #525f7f;
        }

        h1, h2 {
            text-align: center;
        }

        h1 {
            margin: 4%;
            font-size: 2rem;
            font-weight: 10;
        }

        h2 {
            margin: 5%;
            font-size: 4rem;
            font-weight: 100;
        }

        // Timeline styles
        .timeline {
            display: flex;
            flex-direction: column;
            margin: $margin auto;
            position: relative;

            &__event {
            display: flex;
            margin: $spacing 0;
            position: relative;
            border-radius: $bdrs;
            width: 50vw;
            margin-bottom: $spacing;

            &:nth-child(2n + 1) {
                flex-direction: row-reverse;

                .timeline__event__date {
                border-radius: 0 $bdrs $bdrs 0;
                }
                .timeline__event__content {
                border-radius: $bdrs 0 0 $bdrs;
                }

                .timeline__event__icon {
                &:before {
                    content: "";
                    width: 2px;
                    height: 100%;
                    background: $color2;
                    position: absolute;
                    top: 0;
                    left: 50%;
                    transform: translateX(-50%);
                    z-index: -1;
                    animation: fillTop 2s forwards 4s ease-in-out;
                }
                &:after {
                    content: "";
                    width: 100%;
                    height: 2px;
                    background: $color2;
                    position: absolute;
                    top: 50%;
                    left: auto;
                    transform: translateY(-50%);
                    z-index: -1;
                    animation: fillLeft 2s forwards 4s ease-in-out;
                }
                }
            }

            // Event title and content
            &__title {
                font-size: 1.2rem;
                line-height: 1.4;
                text-transform: uppercase;
                font-weight: 600;
                color: $color1;
                letter-spacing: 1.5px;
            }

            &__content {
                padding: $spacing;
                background: #fff;
                box-shadow: 0 30px 60px -12px rgba(50, 50, 93, 0.25),
                            0 18px 36px -18px rgba(0, 0, 0, 0.3),
                            0 -12px 36px -8px rgba(0, 0, 0, 0.025);
                width: $content-width;
                border-radius: 0 $bdrs $bdrs 0;
            }

            // Event date and icon
            &__date {
                color: $color2;
                font-size: 1.5rem;
                font-weight: 600;
                background: $color1;
                display: flex;
                align-items: center;
                justify-content: center;
                white-space: nowrap;
                padding: 0 $spacing;
                border-radius: $bdrs 0 0 $bdrs;
            }

            &__icon {
                display: flex;
                align-items: center;
                justify-content: center;
                color: $color1;
                padding: $spacing;
                background: $color2;
                border-radius: $bdrs-icon;
                width: $circle-size;
                height: $circle-size;
                margin: 0 $spacing;
                box-shadow: 0 30px 60px -12px rgba(50, 50, 93, 0.25),
                            0 18px 36px -18px rgba(0, 0, 0, 0.3),
                            0 -12px 36px -8px rgba(0, 0, 0, 0.025);
                position: relative;

                i {
                font-size: $icon-size;
                }

                &:before {
                content: "";
                width: 2px;
                height: 100%;
                background: $color2;
                position: absolute;
                top: 0;
                left: 50%;
                transform: translateX(-50%);
                z-index: -1;
                animation: fillTop 2s forwards 4s ease-in-out;
                }
                &:after {
                content: "";
                width: 100%;
                height: 2px;
                background: $color2;
                position: absolute;
                top: 50%;
                left: 0;
                transform: translateY(-50%);
                z-index: -1;
                animation: fillLeftOdd 2s forwards 4s ease-in-out;
                }
            }

            &__description {
                flex-basis: 100%;
            }

            // Types of events
            &--type2 {
                &:after {
                background: $color4;
                }
                .timeline__event__date {
                color: $color3;
                background: $color4;
                }

                &:nth-child(2n + 1) {
                .timeline__event__icon {
                    &:before,
                    &:after {
                    background: $color3;
                    }
                }
                }

                .timeline__event__icon {
                background: $color3;
                color: $color4;
                &:before,
                &:after {
                    background: $color3;
                }
                }
                .timeline__event__title {
                color: $color4;
                }
            }

            &--type3 {
                &:after {
                background: $color5;
                }
                .timeline__event__date {
                color: $color6;
                background-color: $color5;
                }

                &:nth-child(2n + 1) {
                .timeline__event__icon {
                    &:before,
                    &:after {
                    background: $color6;
                    }
                }
                }

                .timeline__event__icon {
                background: $color6;
                color: $color5;
                &:before,
                &:after {
                    background: $color6;
                }
                }
                .timeline__event__title {
                color: $color5;
                }
            }

            &:last-child {
                .timeline__event__icon {
                &:before {
                    content: none;
                }
                }
            }
            }
        }

        // Responsive design
        @media (max-width: 786px) {
            .timeline__event {
            flex-direction: column;
            align-self: center;

            &__content {
                width: 100%;
            }

            &__icon {
                border-radius: $bdrs $bdrs 0 0;
                width: 100%;
                margin: 0;
                box-shadow: none;

                &:before,
                &:after {
                display: none;
                }
            }

            &__date {
                border-radius: 0;
                padding: $spacing;
            }

            &:nth-child(2n + 1) {
                flex-direction: column;

                .timeline__event__date {
                border-radius: 0;
                padding: $spacing;
                }

                .timeline__event__icon {
                border-radius: $bdrs $bdrs 0 0;
                margin: 0;
                }
            }
            }
        }

        // Keyframe animations
        @keyframes fillLeft {
            100% {
            right: 100%;
            }
        }

        @keyframes fillTop {
            100% {
            top: 100%;
            }
        }

        @keyframes fillLeftOdd {
            100% {
            left: 100%;
            }
        }
    </style>

</head>
<body>

<script src="https://kit.fontawesome.com/fc596df623.js" crossorigin="anonymous"></script>

<h2>Super Mario Timeline</h2>
<h1>Initial launch dates of games in the Super Mario series.</h1>

<div class="timeline">

	<!--first-->
	<div class="timeline__event  animated fadeInUp delay-3s timeline__event--type1">
		<div class="timeline__event__icon ">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			September 1985
		</div>
		<div class="timeline__event__content ">
			<div class="timeline__event__title">
				Super Mario Brothers
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--second-->

	<div class="timeline__event animated fadeInUp delay-2s timeline__event--type2">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			June 1986
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario Bros: The Lost Levels
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--third-->

	<div class="timeline__event animated fadeInUp delay-1s timeline__event--type3">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			October 1988
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario Bros. 2
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>

		</div>
	</div>

	<!--forth-->

	<div class="timeline__event animated fadeInUp timeline__event--type1">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			October 1988
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario Bros. 3
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--first-->
	<div class="timeline__event  animated fadeInUp delay-3s timeline__event--type1">
		<div class="timeline__event__icon ">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			April 1989
		</div>
		<div class="timeline__event__content ">
			<div class="timeline__event__title">
				Super Mario Land
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--second-->

	<div class="timeline__event animated fadeInUp delay-2s timeline__event--type2">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			November 1990
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario World
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--third-->

	<div class="timeline__event animated fadeInUp delay-1s timeline__event--type3">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			October 1992
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario Land: 6 Golden Coins
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>

		</div>
	</div>

	<!--forth-->

	<div class="timeline__event animated fadeInUp timeline__event--type1">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			August 1995
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario World 2: Yoshi's Island
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--first-->
	<div class="timeline__event  animated fadeInUp delay-3s timeline__event--type1">
		<div class="timeline__event__icon ">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			June 1996
		</div>
		<div class="timeline__event__content ">
			<div class="timeline__event__title">
				Super Mario 64
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--second-->

	<div class="timeline__event animated fadeInUp delay-2s timeline__event--type2">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			July 2002
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario Sunshine
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--third-->

	<div class="timeline__event animated fadeInUp delay-1s timeline__event--type3">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			May 2006
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				New Super Mario Bros
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>

		</div>
	</div>

	<!--forth-->

	<div class="timeline__event animated fadeInUp timeline__event--type1">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			November 2007
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario Galaxy
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--first-->
	<div class="timeline__event  animated fadeInUp delay-3s timeline__event--type1">
		<div class="timeline__event__icon ">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			November 2009
		</div>
		<div class="timeline__event__content ">
			<div class="timeline__event__title">
				New Super Mario Bros. Wii
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--second-->

	<div class="timeline__event animated fadeInUp delay-2s timeline__event--type2">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			May 2010
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario Galaxy 2
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--third-->

	<div class="timeline__event animated fadeInUp delay-1s timeline__event--type3">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			November 2011
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario 3D Land
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>

		</div>
	</div>

	<!--forth-->

	<div class="timeline__event animated fadeInUp timeline__event--type1">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			July 2012
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				New Super Mario Bros 2
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--first-->
	<div class="timeline__event  animated fadeInUp delay-3s timeline__event--type1">
		<div class="timeline__event__icon ">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			November 2012
		</div>
		<div class="timeline__event__content ">
			<div class="timeline__event__title">
				New Super Mario Bros. U
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--second-->

	<div class="timeline__event animated fadeInUp delay-2s timeline__event--type2">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			November 2013
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario 3D World
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--third-->

	<div class="timeline__event animated fadeInUp delay-1s timeline__event--type3">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			September 2015
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario Maker
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>

		</div>
	</div>

	<!--forth-->

	<div class="timeline__event animated fadeInUp timeline__event--type1">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			December 2016
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario Run
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--first-->
	<div class="timeline__event  animated fadeInUp delay-3s timeline__event--type1">
		<div class="timeline__event__icon ">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			October 2017
		</div>
		<div class="timeline__event__content ">
			<div class="timeline__event__title">
				Super Mario Odyssey
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--second-->

	<div class="timeline__event animated fadeInUp delay-2s timeline__event--type2">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			June 2019
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario Maker 2
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

	<!--third-->

	<div class="timeline__event animated fadeInUp delay-1s timeline__event--type3">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			February 2021
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario 3D World + Bowser's Fury
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>

		</div>
	</div>
	<!--forth-->

	<div class="timeline__event animated fadeInUp timeline__event--type1">
		<div class="timeline__event__icon">
			<!-- <i class="lni-sport"></i>-->

		</div>
		<div class="timeline__event__date">
			December 2016
		</div>
		<div class="timeline__event__content">
			<div class="timeline__event__title">
				Super Mario Run
			</div>
			<div class="timeline__event__description">
				<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel, nam! Nam eveniet ut aliquam ab asperiores, accusamus iure veniam corporis incidunt reprehenderit accusantium id aut architecto harum quidem dolorem in!</p>
			</div>
		</div>
	</div>

</div>

</body>
</html>
