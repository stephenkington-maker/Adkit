const nodemailer = require("nodemailer");

export default async function handler(req, res) {
  res.setHeader("Access-Control-Allow-Origin", "*");
  res.setHeader("Access-Control-Allow-Methods", "POST, OPTIONS");
  res.setHeader("Access-Control-Allow-Headers", "Content-Type");

  if (req.method === "OPTIONS") return res.status(200).end();
  if (req.method !== "POST") return res.status(405).json({ success: false });

  const { name, email, company, comments } = req.body || {};

  if (!name || !email || !comments) {
    return res.status(400).json({ success: false, message: "Missing fields" });
  }

  const transporter = nodemailer.createTransport({
    service: "gmail",
    auth: {
      user: "stephenkington@googlemail.com",
      pass: "Chickenwho12!",
    },
  });

  try {
    await transporter.sendMail({
      from: '"Flick Suite" <stephenkington@googlemail.com>',
      to: "stephenkington@googlemail.com",
      replyTo: email,
      subject: "Flick Suite — Sponsorship Enquiry",
      text: [
        "New sponsorship enquiry from Flick Suite",
        "==========================================",
        `Name:     ${name}`,
        `Email:    ${email}`,
        `Company:  ${company || "—"}`,
        "",
        "Campaign details:",
        comments,
        "==========================================",
      ].join("\n"),
    });
    return res.status(200).json({ success: true });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ success: false, message: err.message });
  }
}
