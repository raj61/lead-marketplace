/**
 * Created by ananth on 7/1/17.
 */
(function (angular) {
	'use strict';
	angular.module('leadPortalModule', ['ngAnimate'])
		.controller('leadsFromAPI', ['$scope', '$http', function ($scope, $http) {
			$scope.toggle_card_hidden = function (card) {
				card.isHidden = !card.isHidden;
			};
			$scope.cards = [];
			$http({
				url: '/wp-json/marketplace/v1/leads/details',
				cache: true
			})
				.success(function (data, status, headers, config) {
					// this callback will be called asynchronously
					// when the response is available
					for (var index = 0; index < data.length; ++index) {
						var card = data[index].lead_card;
						var current_card = {
							Name: card.name,
							Location: card.location,
							Category: card.category,
							Query: card.query,
							isHidden: card.isHidden
						};
						$scope.cards.push(current_card);
					}
				})
				.error(function (data, status, header, config) {
					// called asynchronously if an error occurs
					// or server returns response with an error status.
					alert("Unable to fetch the lead details from the API.");
				});
		}]);
})(window.angular);
