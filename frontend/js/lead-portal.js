/**
 * Created by ananth on 7/1/17.
 */
(function (angular) {
	'use strict';
	angular.module('leadPortalModule', ['ngAnimate'])
		.controller('leadsFromAPI', ['$scope', '$http', function ($scope, $http) {
			$scope.cardSelectionCriteria = function(card){
				if(!$scope.user_hidden && card.isHidden) {
					return false;
				}
				if($scope.userSelectedLocations!=0 && !($scope.containsInArray($scope.userSelectedLocations,card.Location))) {
					return false;
				}
				if($scope.userSelectedCategories!=0 && !($scope.containsInArray($scope.userSelectedCategories,card.Category))) {
					return false;
				}
				return true;
			};
			$scope.containsInArray = function(a, obj) {
				for (var i = 0; i < a.length; i++) {
					if (a[i] === obj) {
						return true;
					}
				}
				return false;
			}
			$scope.userSelectedLocations = [];
			$scope.userSelectedCategories = [];
			$scope.setSelectedCategories = function(prop){
				if (!($scope.containsInArray($scope.userSelectedCategories, prop.name))) {
					$scope.userSelectedCategories.push(prop.name);
				}else{
					removeItemFromArray($scope.userSelectedCategories, prop.name);
				}
			};
			$scope.setSelectedLocations = function(prop){
				if (!($scope.containsInArray($scope.userSelectedLocations, prop.name))) {
					$scope.userSelectedLocations.push(prop.name);
				}else {
					removeItemFromArray($scope.userSelectedLocations, prop.name);
				}
			};
			$scope.toggle_card_hidden = function (card) {
				card.isHidden = !card.isHidden;
			};
			$scope.unlock_card_if_possible = function (card) {
				card.isUnlocked = !card.isUnlocked;
			};
			$scope.cards = [];
			$scope.topLocations = [];
			$scope.topCategories = [];
			function populateScopevariablesFromAPI(data) {
				var locationCount = {};
				var categoryCount = {};
				for (var index = 0; index < data.length; ++index) {
					var card = data[index].lead_card;
					var locationInt = ++locationCount[card.location];
					var catrgoryInt = ++categoryCount[card.category];
					if (isNaN(locationInt)) {
						locationCount[card.location] = locationInt = 1;
					}
					if (isNaN(catrgoryInt)) {
						categoryCount[card.category] = catrgoryInt = 1;
					}
					var currentLocation = {
						Name: card.location,
						Count: locationInt
					};
					var currentCategory = {
						Name: card.category,
						Count: catrgoryInt
					};
					$scope.cards.push(card);
					var isExistingLocation = false;
					var isExistingCategory = false;
					for (var topLocation in $scope.topLocations) {
						if (topLocation.Name == currentLocation.Name) {
							isExistingLocation = true;
							topLocation.Count = locationInt;
						}
					}
					for (var topCategory in $scope.topCategories) {
						if (topCategory.Name == currentCategory.Name) {
							isExistingCategory = true;
							topCategory.Count = catrgoryInt;
						}
					}
					if (!isExistingLocation) {
						$scope.topLocations.push(currentLocation);
					}
					if (!isExistingCategory) {
						$scope.topCategories.push(currentCategory);
					}
				}
			};
			$http({
				url: '/wp-json/marketplace/v1/leads/details',
				cache: true
			})
				.success(function (data, status, headers, config) {
					// this callback will be called asynchronously
					// when the response is available
					populateScopevariablesFromAPI(data);
				})
				.error(function (data, status, header, config) {
					// called asynchronously if an error occurs
					// or server returns response with an error status.
					alert("Unable to fetch the lead details from the API.");
				});
		}]);
})(window.angular);

