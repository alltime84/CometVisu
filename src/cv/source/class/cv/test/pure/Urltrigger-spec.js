/**
 * Unit tests for urltrigger widget
 *
 */
describe("testing a urltrigger widget", function() {

  it("should test the urltrigger creator", function() {

    var res = this.createTestWidgetString("urltrigger", {}, '<label>Test</label>');
    var widget = qx.bom.Html.clean([res[1]])[0];
    expect(res[0].getPath()).toBe("id_0");

    expect(widget).toHaveClass('trigger');
    expect(widget).toHaveLabel('Test');
  });
});