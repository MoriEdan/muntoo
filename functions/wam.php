<?php

// =======TF-IDF=======

// tf-idf , The Formula
function tf_idf($tf_arr, $idf_arr){
	return  ( $tf_arr[0] / $tf_arr[1] ) * log10( $idf_arr[0] / $idf_arr[1] );
}
	
// TF-IDF
// Weights actually should be assigned dynamically, 
// Like
// The more the Frequency of an action occuring, the lesser the weight(comparative weight) it has
// For eg. If, action like 'profile_visits' are happening too often, across the site, 
// then, the weight of 'profile_visits' , may go even further down & fall below 0.2 , as 0.1, 0.05 whatever and so on...
// depending upon the calculation
/*
 * Consider a document containing 100 words wherein the word cat appears 3 times. The term frequency (i.e., tf) for cat is then (3 / 100) = 0.03. Now, assume we have 10 million documents and the word cat appears in one thousand of these. Then, the inverse document frequency (i.e., idf) is calculated as log(10,000,000 / 1,000) = 4. Thus, the Tf-idf weight is the product of these quantities: 0.03 * 4 = 0.12.
 * */
// tf = nmber_times_word_appears_(1_document) / number_of_words_in_the_doc
// idf = log( nmbr_of_documents_in_the_corpus / numbr_of_docs_in_which_word_appears)
// tf-idf = tf*idf

$tf_idf_arr = array (

	'mutual_friend' => array (
		// tf - Number of times action happens, and is present in the database
		'tf' => array( 10, 100 ) , 
		// idf - Number of documents in total, and numbr of docs where this action appeared
		'idf' => array( 1000, 100 ) 
	),
	
	'profile_visits' => array (
		'tf' => array( 10, 100 ) , 
		'idf' => array( 1000, 500 ) 
	),
	
	'wall_posts' => array (
		'tf' => array( 10, 100 ) , 
		'idf' => array( 1000, 500 ) 
	),
	
	'like' => array (
		'tf' => array( 10, 100 ) , 
		'idf' => array( 1000, 700 ) 
	),
	
	'share' => array (
		'tf' => array( 10, 100 ) , 
		'idf' => array( 1000, 800 ) 
	),
	
	'album_visits' => array (
		'tf' => array( 10, 100 ) , 
		'idf' => array( 1000, 900 ) 
	),
	
);

$tf_idf_weights = array();
foreach( $tf_idf_arr as $k => $v) {
	// The Formula
	$tf_idf = tf_idf( $v['tf'], $v['idf'] ) ;
	
	$tf_idf = round( $tf_idf, 10);
	
	$tf_idf_weights[$k] = $tf_idf;
}

// print_r( $tf_idf_weights );



// =======WAM=======

// WAM: Weighted Average Mean Method

// The tables/arrays are with reference to, user id 1, and the 2 users are not each other's friends yet, so that we can make friend suggestion starts
// how many profile_visits made on user id 1's (on_uid) profile, by another user (by_uid)
// how many posts were liked made of user 1's (of_uid) by another user (by_uid)
// how many times album/or photo of user id 1 (of_uid) visited by another user (by_uid)

/*
// Mutual_friends table
calling all his friends (array), and
calling all her friends (array)
then,
merging the array
*/

//  id	mutual_friend		profile_visits			wall_posts		like			share			album_visits			weight_amount
//  1				1									5									5					10				20					40									1

// so how much weight does a quantity(1 unit) of each have,
// for .eg. 1 mutual_friend has weight 1, 1 profile_visits has weight 0.2, and so on
// so, over here in the below array, 
// quantity is constant, 1 for each, mutual_friend, profile_visits, but weight differs.
// So, quantity => weight table, 1 unit of each quantity has how much weight
/*
 * // Calculating Weights, Importance of an Action:
 * Try Calculate the actualy weight by an algorithm, 
 * which takes in the frequency of the actions (for eg. like, share)
 * so, how frequent is the action, 
 * and therefore assigning Importance to an action.
 * Mostly, the more frequent the action is, 
 * the Lesser the importance it might hold.
*/
$weights = array(
	
	'weights' => array(
		'mutual_friend' => 1,
		'profile_visits' => 0.2,
		'wall_posts' => 0.2,
		'like' => 0.1,
		'share' => 0.05,
		'album_visits' => 0.025
	),
	
	'reasons' => array(
		'mutual_friend' => 'Friend suggested mainly because you have mutual friends: amt_of_friends',
		'profile_visits' => 'Suggested mainly because you tend to visit thier profile too often: Around amt_of_times', // (* maybe per_day value)',
		'wall_posts' => 'Suggested because you have made previous wall post on their profile: Around amt_of_wall_posts(per day)',
		'like' => 'You have liked thier posts: Around amt_of_likes_per_day',
		'share' => 'You have shared thier posts: Around amt_of_shares_per_day',
		'album_visits' => 'You have made album visits on their albums: Around amt_of_album_visits_per_day'
	),
	
	
);

// Profile_visits table
// id		on_uid		by_uid		time
// Just creating a table here, writing an array depicting a table.
// This array depicts, whose profile uid 1 is visiting more & more often
/*
$profile_visits = array(
	'id' => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
	'on_uid' => array(2, 2, 2, 2, 2, 3, 3, 3, 4, 4),
	'by_uid' => array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
	'time' => array()
);
*/
$profile_visits = array(
	'id' => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
	'on_uid' => array(2, 2, 2, 2, 2, 3, 3, 3, 4, 4),
	'by_uid' => array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
	'time' => array()
);


// IMPORTANT here too
// This array depicts, that, uid 2,3,4 have made profile visits on uid 1's profile
// for the moment we are not taking this array,
// this maybe useful later as,
// reciprocal visits
/*
$profile_visits = array(
	'id' => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
	'on_uid' => array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
	'by_uid' => array(2, 2, 2, 2, 2, 3, 3, 3, 4, 4),
	'time' => array()
);
*/


// Wall_posts table


// Like's table, aka Post table, where posts of one user were liked by another
// Post_id was liked by these many people


// album_visits table
$album_visits = array(
	'id' => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
	'on_uid' => array(2, 2, 2, 3, 3, 3, 3, 3, 4, 4),
	'by_uid' => array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
	'time' => array()
);


// Array depicts that, how many album visits have uids 2,3,4 
// have made on uid 1's profile
// Not taking this array at the moment
/*
$album_visits = array(
	'id' => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
	'on_uid' => array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
	'by_uid' => array(2, 2, 2, 3, 3, 3, 3, 3, 4, 4),
	'time' => array()
);
*/


/*
wam = ( X1W1 + X2W2 + .... + XnWn ) / (W1 + W2 + ... + Wn )

so,
* uid 1 made how many profile visits on uid 2, total 4
* so for uid 2,
* profile_visits = 1(0.2) + 1(0.2) + 1(0.2) + 1(0.2) / 0.2 + 0.2 + 0.2 + 0.2
* { we can also take, Naive Bayes approach here,
* where, uid 1 has visited profiles in all, only,  9 times, 
* and out of that 9 times, 4 are on uid 2
* so wat is the probablity, that the next profile he (uid1) is visiting 
* is of uid2
* But, Naive Bayes should most of the times be used for, 
* probablity purposes, for predicting future which is unknown. ( *maybe used)
* }
* 
* 
* uid 1 made how many album visits on uid 2, total 3
* so for uid 2,
* album_visits = 1(0.025) + 1(0.025) + 1(0.025) / 0.025 + 0.025 + 0.025


*/

$pv = array();
$av = array();


$pv_str = 'profile_visits';
$av_str = 'album_visits';

$w_str = 'weights';

$pv_w = ( ${"$w_str"}[$w_str][$pv_str] );
//echo '<br />';
$av_w = ( ${"$w_str"}[$w_str][$av_str] );


// echo '<pre>';
// print_r($profile_visits);


foreach($profile_visits['on_uid'] as $k => $v)
{
	// uid as the value in the array, 
	// multiple times visit to the uid, will have the same uid multiple times as the value
	// in the array
	// so if it is isset(), then +1, else just 1
	if(isset($pv[$v]) )
		$pv[$v] += 1; //* $pv_w;
	else
		$pv[$v] = 1; //*$pv_w;
}


foreach($album_visits['on_uid'] as $k => $v)
{
	if(isset($av[$v]) )
		$av[$v] +=  1; // * $av_w;
	else
		$av[$v] = 1; // * $av_w;
}

//print_r($pv);
//print_r($av);

foreach($pv as $k => $v)
{
	$friend_reco[$k]['points'] = ( $pv[$k] * $pv_w ) + ( $av[$k] * $av_w );
	$friend_reco[$k]['reasons'] = '(1) ' . $weights['reasons'][$pv_str] . ' & (2) '  . $weights['reasons'][$av_str];
}

//print_r($final);

arsort($friend_reco);


//print_r($final_reco);


?>

