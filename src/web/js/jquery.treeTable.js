/*
 * jQuery treeTable Plugin
 *
 * Copyright 2012, Grégoire Dubourg
 * Dual licensed under the MIT or GPL Version 2 licenses.
 */
(function($) {
  // Helps to make options available to all functions
  var options;

  $.fn.treeTable = function(opts) {
    options = $.extend({}, $.fn.treeTable.defaults, opts);

    return this.each(function() {
      $(this).find("tbody tr").each(function() {
        initialize($(this));
      });
    });
  };

  $.fn.treeTable.defaults = {
    dataAttribute: "level",
    collapsedByDefault: true,
    ignoreClickOn: "input, a"
  };

  // Recursively hide all node's children in a tree
  $.fn.treetable_collapse = function() {
    if ($(this).treetable_hasChildren()) {
      $(this).removeClass("expanded").addClass("collapsed");
      childrenOf($(this)).each(function() {
        $(this).hide().treetable_collapse();
      });
    }
    return this;
  };

  // Recursively show all node's children in a tree
  $.fn.treetable_expand = function() {
    if ($(this).treetable_hasChildren()) {
      $(this).removeClass("collapsed").addClass("expanded");
      childrenOf($(this)).each(function() {
        $(this).show();
      });
    }
    return this;
  };

  // Check if node has children
  $.fn.treetable_hasChildren = function() {
    return (childrenOf($(this)).length > 0);
  };

  // treetable_toggle an entire branch
  $.fn.treetable_toggle = function() {
    if ($(this).hasClass("collapsed"))
      $(this).treetable_expand();
    else
      $(this).treetable_collapse();
    return this;
  };

  // === Private functions

  function initialize(node) {
    if (node.treetable_hasChildren()) {
      node.click(function(event) {
        var $target = $(event.target);
        if (!$target.is(options.ignoreClickOn)) {
          node.treetable_toggle();
          return false;
        }
      });
      if (options.collapsedByDefault)
        node.treetable_collapse();
      else
        node.treetable_expand();
    }
  };

  function getLevel(node) {
    return parseInt($(node).data(options.dataAttribute));
  };

  function childrenOf(node) {
    nodeLevel = getLevel(node);
    childrenLevel = nodeLevel + 1;
    return $(node).nextUntil("tr[data-" + options.dataAttribute + "=" + nodeLevel + "]", "tr[data-" + options.dataAttribute + "=" + childrenLevel + "]");
  };
})(jQuery);
