var mongoose;
var Schema;

function connectDb() {
    var mongoose = require('mongoose/'),
        db = mongoose.connect('mongodb://cargo.dev:27017'),//TODO
        Schema = mongoose.Schema,
        ObjectId = mongoose.SchemaTypes.ObjectId;
    this.mongoose = mongoose;
    this.Schema = Schema;
}

module.exports.connectDb = connectDb;
module.exports.mongoose = mongoose;
module.exports.Schema = Schema;